<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMemberRequest;
use App\Http\Resources\MemberResource;
use App\Models\Member;
use Illuminate\Http\Request;
use PhpParser\Node\Stmt\Catch_;
use PhpParser\Node\Stmt\TryCatch;
use PHPUnit\Event\TestRunner\ExecutionStarted;

class MemberController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Member::with('activeBorrowings');
        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where(function ($e) use ($search) {
                $e->where('name', 'LIKE', "%{$search}%")
                    ->orWhere('email', 'LIKE', "%{$search}%");
            });
        }
        if ($request->has('status')) {
            $query->where('status', 'borrowed');
        }
        $members = $query->orderBy('id', 'desc')->paginate(10);
        return MemberResource::collection($members);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreMemberRequest $request)
    {
        $member = Member::create($request->validated());
        return new MemberResource($member);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $member = Member::findOrFail($id);

            return new MemberResource($member);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Not found'
            ], 404);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(StoreMemberRequest $request, Member $member)
    {
        $member->update($request->validated());
        return new MemberResource($member);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Member $member)
    {
        if ($member->activeBorrowings()->count() > 0) {
            return response()->json([
                'message' => 'Cannot delete member with active borrowings'
            ], 403);
        }
        $member->delete();
        return response()->json([
            'message' => 'Member deleted successfully'
        ], 200);
    }
}
