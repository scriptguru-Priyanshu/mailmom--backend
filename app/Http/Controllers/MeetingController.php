<?php

namespace App\Http\Controllers;

use App\Mail\MeetingMail;
use App\Models\Meeting;
use App\Models\Task;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;


class MeetingController
{
    //

    public function approve(Request $request, Meeting $meeting)
    {
        if ($meeting->user_id !== auth()->id()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }
        $meeting->update([
            'status' => 'approved',
        ]);
        $users = User::whereIn('email', $meeting->participants)->get();

        foreach ($meeting->action_items as $task) {
            $ownerName = strtolower(trim($task->owner));

            if (is_array($task->tasks)) {
                if (empty($task->tasks)) {
                    return;
                }
            } else {
                $taskArray = json_decode($task->tasks);
                if (empty($taskArray)) {
                    return;
                }
            }
            // fetch all the users from participants emails


            foreach ($users as $user) {
                if ($user->name === $ownerName) {
                    foreach ($task->tasks as  $t) {
                        Task::create([
                            'meeting_id' => $meeting->id,
                            'owner_id' => $user->id,
                            'task' => $t,
                            'deadline' => $task['deadline'],
                            'status' => 'pending'
                        ]);
                    }
                }
            }

            foreach ($meeting->participants as $email) {
                try {
                    Mail::to($email)->queue(new MeetingMail($meeting));
                    Log::info("Email sent to address: {$email}");
                } catch (Exception $e) {
                    Log::error($e->getMessage());
                    return response()->json([
                        'success' => false,
                        'error' => $e->getMessage()
                    ], 500);
                }
            }
        }

        return response()->json([
            'success' => true,
            'message' => "Mom approved successfully"
        ], 200);
    }

    public function regenerate(Request $request, Meeting $meeting)
    {
        $prompt = "
        You are an AI meeting assistant.

        Analyze this transcript and generate:
        - Summary
        - Key points
        - Decisions
        - Action items

        Return only valid JSON.

        {
            \"summary\": \"\",
            \"key_points\": [],
            \"decisions\": [],
            \"action_items\": [
                {
                \"tasks\": [],
                \"owner\": \"\",
                \"deadline\": \"\"
                }
            ]
        }

        keep in check, if a owner has 2 task, don't create 2 entries but put both task strings in the task key of owner, which is an array.

        ";
        try {
            $apiKey = env('GEMINI_API_KEY');

            // 1. The URL must include the full path, model name, and the key query parameter
            $url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent?key=" . $apiKey;

            $response = Http::withHeaders([
                'Content-Type' => 'application/json'
            ])->post($url, [
                'contents' => [
                    [
                        'parts' => [
                            [
                                'text' => $prompt . ' Transcript: ' . $meeting->transcript
                            ]
                        ]
                    ]
                ]
            ]);

            // Check if Google returned an error status (like 400 or 403)
            if ($response->failed()) {
                dd('Google API Error:', $response->json());
            }

            // Success! Extract the text
            $aiOutput = $response->json()['candidates'][0]['content']['parts'][0]['text'];
            $aiOutput = str_replace('```json', '', $aiOutput);
            $aiOutput = str_replace('```', '', $aiOutput);
            $data = json_decode($aiOutput, true);
            $meeting->update([
                'summary' => $data['summary'],
                'action_items' => $data['action_items'],
                'key_points' => $data['key_points'],
                'decisions' => $data['decisions']
            ]);


            return response()->json([
                'success' => true,
                'data' => $meeting
            ], 201);
        } catch (Exception $e) {
            dd('Connection Error: ' . $e->getMessage());
        }
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string',
            'participants' => 'array',
            'transcript' => 'required|string',
            'scheduled_at' => 'required|date'
        ]);
        $user = auth('sanctum')->user();

        $title = $request->title;
        $transcript = $request->transcript;

        $participants = $request->participants;

        foreach ($participants as $participant) {
            $email = $participant;
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                return response()->json([
                    'success' => false,
                    'message' => "{$email} is an invalid email",
                    'error_code' => "INVALID_EMAIL",
                    'invalid_email' => $email
                ], 422);
            }
            if (!User::where('email', $email)->exists()) {
                return response()->json([
                    'success' => false,
                    'message' => "{$email} doesn't exists",
                    'email' => $email,
                    'error_code' => "EMAIL_DOESN'T_EXISTS"
                ], 400);
            }
        }

        $prompt = "
        You are an AI meeting assistant.

        Analyze this transcript and generate:
        - Summary
        - Key points
        - Decisions
        - Action items

        Return only valid JSON.

        {
            \"summary\": \"\",
            \"key_points\": [],
            \"decisions\": [],
            \"action_items\": [
                {
                \"tasks\": [],
                \"owner\": \"\",
                \"deadline\": \"\"
                }
            ]
        }

        keep in check, if a owner has 2 task, don't create 2 entries but put both task strings in the task key of owner, which is an array.

        ";

        try {
            $apiKey = env('GEMINI_API_KEY');

            // 1. The URL must include the full path, model name, and the key query parameter
            $url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent?key=" . $apiKey;

            $response = Http::withHeaders([
                'Content-Type' => 'application/json'
            ])->post($url, [
                'contents' => [
                    [
                        'parts' => [
                            [
                                'text' => $prompt . ' Transcript: ' . $request->transcript
                            ]
                        ]
                    ]
                ]
            ]);

            // Check if Google returned an error status (like 400 or 403)
            if ($response->failed()) {
                dd('Google API Error:', $response->json());
            }

            // Success! Extract the text
            $aiOutput = $response->json()['candidates'][0]['content']['parts'][0]['text'];
            $aiOutput = str_replace('```json', '', $aiOutput);
            $aiOutput = str_replace('```', '', $aiOutput);
            $data = json_decode($aiOutput, true);
            $meeting = Meeting::create([
                'title' => $request->title,
                'summary' => $data['summary'],
                'transcript' => $transcript,
                'action_items' => $data['action_items'],
                'key_points' => $data['key_points'],
                'decisions' => $data['decisions'],
                'participants' => $request->participants,
                'user_id' => $user->id,
                'scheduled_at' => $request->scheduled_at,
                'status' => 'pending'
            ]);


            return response()->json([
                'success' => true,
                'data' => $meeting
            ], 201);
        } catch (Exception $e) {
            dd('Connection Error: ' . $e->getMessage());
        }
    }
    public function index(Request $request)
    {
        $search = $request->search;

        $meetings = Meeting::query()

            ->when($search, function ($query) use ($search) {

                $query->where('title', 'LIKE', "%{$search}%")

                    ->orWhere('summary', 'LIKE', "%{$search}%")

                    ->orWhere('transcript', "LIKE", "%{$search}%");
            })

            ->withCount('tasks')

            ->latest()

            ->paginate(10);

        return response()->json([

            'success' => true,

            'data' => $meetings
        ]);
    }


    public function show(Meeting $meeting)
    {
        $meeting->load([
            'tasks.owner:id,name,email'
        ]);

        return response()->json([

            'success' => true,

            'data' => [

                'id' => $meeting->id,

                'title' => $meeting->title,

                'summary' => $meeting->summary,
                'participants' => $meeting->participants,
                'transcript' => $meeting->transcript,

                'key_points' => $meeting->key_points,

                'decisions' => $meeting->decisions,

                'tasks' => $meeting->tasks->count()
                    ? $meeting->tasks
                    : $meeting->action_items
            ]
        ]);
    }


    public function dashboardStats()
    {
        $userId = Auth::id();

        // Get only meeting IDs relevant to the user
        $meetingIds = Meeting::query()
            ->where('user_id', $userId)
            ->orWhereJsonContains('participants', $userId)
            ->pluck('id');

        // Meetings count
        $meetingsCount = $meetingIds->count();

        //  Tasks count (across those meetings)
        $tasksCount = Task::whereIn('meeting_id', $meetingIds)->count();

        //  Unique participants count (from JSON)
        $participantsCount = Meeting::whereIn('id', $meetingIds)
            ->get()
            ->pluck('participants')
            ->flatten()
            ->filter()
            ->unique()
            ->count();

        return response()->json([
            'meetings_count' => $meetingsCount,
            'tasks_count' => $tasksCount,
            'participants_count' => $participantsCount,
        ]);
    }
}
