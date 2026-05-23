<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;

class TaskController
{
    //
    /**
     * ============================
     * Get Tasks Assigned To Me
     * ============================
     */
    public function myTasks(Request $request)
    {
        $search = $request->search;

        $status = $request->status;

        $tasks = Task::query()

            ->where('owner_id', auth()->id())

            ->when($search, function ($query) use ($search) {

                $query->where('task', 'LIKE', "%{$search}%");
            })

            ->when($status, function ($query) use ($status) {

                $query->where('status', $status);
            })

            ->with([
                'meeting:id,title,scheduled_at',
                'owner:id,name,email'
            ])

            ->latest()

            ->paginate(10);

        return response()->json([

            'success' => true,

            'message' => 'My tasks fetched successfully',

            'data' => $tasks
        ]);
    }

    /**
     * ==================================
     * Get Tasks From Meetings I Created
     * ==================================
     */
    public function myMeetingTasks(Request $request)
    {
        $search = $request->search;

        $status = $request->status;

        $tasks = Task::query()

            ->whereHas('meeting', function ($query) {

                $query->where('user_id', auth()->id());
            })

            ->when($search, function ($query) use ($search) {

                $query->where('task', 'LIKE', "%{$search}%");
            })

            ->when($status, function ($query) use ($status) {

                $query->where('status', $status);
            })

            ->with([
                'meeting:id,title,scheduled_at',
                'owner:id,name,email'
            ])

            ->latest()

            ->paginate(10);

        return response()->json([

            'success' => true,

            'message' => 'Meeting tasks fetched successfully',

            'data' => $tasks
        ]);
    }

    /**
     * ======================
     * Get Single Task
     * ======================
     */
    public function show(Task $task)
    {
        // Authorization check
        $isOwner = $task->owner_id === auth()->id();

        $isMeetingCreator =
            $task->meeting->user_id === auth()->id();

        if (!$isOwner && !$isMeetingCreator) {

            return response()->json([

                'success' => false,

                'message' => 'Unauthorized'
            ], 403);
        }

        $task->load([
            'meeting:id,title,summary,scheduled_at',
            'owner:id,name,email'
        ]);

        return response()->json([

            'success' => true,

            'data' => $task
        ]);
    }

    /**
     * ==========================
     * Update Task Status
     * ==========================
     */
    public function updateStatus(Request $request, Task $task)
    {
        $request->validate([

            'status' => 'required|in:pending,in_progress,completed'
        ]);

        // Only assigned owner can update
        if ($task->owner_id !== auth()->id()) {

            return response()->json([

                'success' => false,

                'message' => 'Only task owner can update status'
            ], 403);
        }

        $task->update([

            'status' => $request->status
        ]);

        return response()->json([

            'success' => true,

            'message' => 'Task status updated successfully',

            'data' => $task
        ]);
    }

    /**
     * ==========================
     * Dashboard Task Statistics
     * ==========================
     */
    public function stats()
    {
        $myTasks = Task::where('owner_id', auth()->id());

        $stats = [

            'total_tasks' => $myTasks->count(),

            'pending_tasks' => (clone $myTasks)
                ->where('status', 'pending')
                ->count(),

            'in_progress_tasks' => (clone $myTasks)
                ->where('status', 'in_progress')
                ->count(),

            'completed_tasks' => (clone $myTasks)
                ->where('status', 'completed')
                ->count()
        ];

        return response()->json([

            'success' => true,

            'data' => $stats
        ]);
    }
}
