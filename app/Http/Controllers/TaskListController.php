<?php

namespace App\Http\Controllers;

use App\Models\TaskList;
use App\Http\Requests\StoreTaskListRequest;
use App\Http\Requests\UpdateTaskListRequest;
use Illuminate\Support\Facades\Auth;

class TaskListController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $loggedInUser = Auth::user();

        if ($loggedInUser->role === 'admin') {
            $taskLists = TaskList::all()->paginate(10);
        } else {
            $taskLists = TaskList::where('user_id', $loggedInUser->id)->get();
        }

        return response([
            'status' => 'success',
            'result' => $taskLists
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
     * @param  \App\Http\Requests\StoreTaskListRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreTaskListRequest $request)
    {
        $loggedInUser = Auth::user();

        $taskList = TaskList::create([
            'name' => $request->validated()['name'],
            'status' => $request->validated()['status'],
            'user_id' => $loggedInUser->id,
        ]);

        return response([
            'status' => 'success',
            'result' => $taskList
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\TaskList  $taskList
     * @return \Illuminate\Http\Response
     */
    public function show(TaskList $taskList)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\TaskList  $taskList
     * @return \Illuminate\Http\Response
     */
    public function edit(TaskList $taskList)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateTaskListRequest  $request
     * @param  \App\Models\TaskList  $taskList
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateTaskListRequest $request, int $id)
    {
        $loggedInUser = Auth::user();

        $taskList = TaskList::where('id', $id)->first();

        $taskList->update([
            'name' => $request->validated()['name'],
            'status' => $request->validated()['status'],
            'user_id' => $loggedInUser->id,
        ]);

        return response([
            'status' => 'success',
            'result' => $taskList
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\TaskList  $taskList
     * @return \Illuminate\Http\Response
     */
    public function destroy(int $id)
    {
        $loggedInUser = Auth::user();

        $taskList = TaskList::where('id', $id)->first();
        if (!$taskList) {
            return response(
                [
                    'status' => 'Error',
                    'result' => []
                ],
                404
            );
        }

        if ($taskList->user_id !== $loggedInUser->id) {
            return response(
                [
                    'status' => 'Error',
                    'result' => []
                ],
                403
            );
        }
        $taskList->delete();

        return response([
            'status' => 'success',
            'result' => $id
        ]);
    }
}