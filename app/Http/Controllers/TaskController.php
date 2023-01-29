<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Models\TaskList;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class TaskController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // $loggedInUser = Auth::user();
        $tasks = Task::all();

        // if ($loggedInUser->role === 'admin') {
        //     $tasks = Task::all()->paginate(10);
        // } else {
        //     $tasks = Task::where('user_id', $loggedInUser->id)->get();
        // }

        return response([
            'status' => 'success',
            'result' => $tasks
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreTaskRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreTaskRequest $request)
    {
        // $loggedInUser = Auth::user();

        // $tasks = Task::where('task_list_id', $request->validated()['task_list_id'])->get();
        // $tasksCompleted = Task::where('task_list_id', $request->validated()['task_list_id'])
        //     ->where('status', 'completed')
        //     ->get();
        // if ((count($tasks) === count($tasksCompleted)) && $request->validated()['status'] === 'completed') {
        //     $taskList = TaskList::find($request->validated()['task_list_id']);
        //     $taskList->status = $request->validated()['status'];
        //     $taskList->save();
        // }
        $task = Task::create([
            'name' => $request->validated()['name'],
            'description' => $request->validated()['description'],
            'due_date' => $request->validated()['due_date'],
            // 'status' => $request->validated()['status'],
            'task_list_id' => $request->validated()['task_list_id'],
        ]);

        if ($request->validated()['status'] === 'completed') {
            $task->markAsComplete();
        } else {
            $task->markAsPending();
        }

        return response([
            'status' => 'success',
            'result' => $task
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Task  $task
     * @return \Illuminate\Http\Response
     */
    public function show(Task $task)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Task  $task
     * @return \Illuminate\Http\Response
     */
    public function edit(Task $task)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateTaskRequest  $request
     * @param  \App\Models\Task  $task
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateTaskRequest $request, int $id)
    {

        $task = Task::where('id', $id)->first();
        if (!$task) {
            return response(
                [
                    'status' => 'failed',
                    'result' => []
                ],
                404
            );
        }

        $task->update([
            'name' => $request->validated()['name'],
            'description' => $request->validated()['description'],
            'due_date' => $request->validated()['due_date'],
            // 'status' => $request->validated()['status'],
            // 'task_list_id' => $request->validated()['task_list_id'],
        ]);
        if ($request->validated()['status'] === 'completed') {
            $task->markAsComplete();
        } else {
            $task->markAsPending();
        }
        return response([
            'status' => 'success',
            'result' => $task
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Task  $task
     * @return \Illuminate\Http\Response
     */
    public function destroy(int $id)
    {
        // $loggedInUser = Auth::user();
        $loggedInUser = User::find(1);

        $task = Task::where('id', $id)->first();

        if (!$task) {
            return response(
                [
                    'status' => 'Error',
                    'result' => []
                ],
                404
            );
        }
        $taskList = TaskList::where('user_id', $loggedInUser->id)->first();
        if ($task->task_list_id !== $taskList->id) {
            return response(
                [
                    'status' => 'Error',
                    'result' => []
                ],
                403
            );
        }
        $task->delete();

        $taskList->updateStatus();
        return response([
            'status' => 'success',
            'result' => $id
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function dashboard()
    {
        $loggedInUser = Auth::user();

        $thisWeekStart = Carbon::now()->startOfWeek();
        $thisWeekEnd = Carbon::now()->endOfWeek();

        if ($loggedInUser->role === 'client') {

            $taksLists = TaskList::where('user_id', $loggedInUser->id)
                ->get();
            $validIds = [];
            foreach ($taksLists as $taksList) {
                $validIds[] = [
                    $taksList->id
                ];
            }
            $thisWeekTasks = Task::whereIn('task_list_id', $validIds)
                ->whereBetween('due_date', [$thisWeekStart, $thisWeekEnd])
                ->get();

            $completedTasks = Task::whereIn('task_list_id', $validIds)
                ->where('status', 'completed')->get();

            $pendingTasks = Task::whereIn('task_list_id', $validIds)
                ->where('status', 'pending')->get();

            $history = Task::whereIn('task_list_id', $validIds)
                ->orderBy('updated_at', 'desc')->take(10)->get();

            return response([
                'status' => 'success',
                'result' => [
                    'taksLists' => count($taksLists),
                    'thisWeekTasks' => count($thisWeekTasks),
                    'lists' => $history,
                    'completedTasks' => count($completedTasks),
                    'pendingTasks' => count($pendingTasks),
                ]
            ]);
        }

        $users = User::whereNot('id', $loggedInUser->id)->get();

        $thisWeekUsers = User::whereBetween('created_at', [$thisWeekStart, $thisWeekEnd])
            ->get();

        $lists = TaskList::all();

        $completedTasks = Task::where('status', 'completed')->get();

        $pendingTasks = Task::where('status', 'pending')->get();

        return response([
            'status' => 'success',
            'result' => [
                'users' => count($users),
                'thisWeekUsers' => count($thisWeekUsers),
                'lists' => count($lists),
                'completedTasks' => count($completedTasks),
                'pendingTasks' => count($pendingTasks),
            ]
        ]);
    }
}