<?php

namespace App\Http\Middleware;

use Symfony\Component\HttpFoundation\Response;
use App\Events\TaskUpdating;
use Illuminate\Http\Request;
use Closure;

class CheckTaskUpdateAuthorization
{
    /**
     * Send this message if deadline has expired.
     * 
     * @const
     */
    const DEADLINE_HAS_EXPIRED = 'The deadline of the task has expired';

    /**
     * Send this message if admin tries to access tasks the have
     * no expired deadline.
     * 
     * @const
     */
    const ACCESS_TO_NOT_EXPIRED_DEADLINES_FORBIDDEN = 
        'You can not access task with deadlines that have not expired.';

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!$request->routeIs('tasks.update')) {
            return $next($request);
        }

        $task = $request->route('task');

        if (!$task) {
            return $next($request);
        }

        $isAdmin = auth()->user()?->isAdmin() ?? false;
        $isOverdue = now() > $task->deadline;
        $isOwner = $task->user_id == auth()->id();

        abort_if(
            $isAdmin && !$isOverdue, 
            Response::HTTP_FORBIDDEN, 
            self::ACCESS_TO_NOT_EXPIRED_DEADLINES_FORBIDDEN
        );

        TaskUpdating::dispatchIf($isOverdue && $isOwner, $task);

        abort_if(
            $isOverdue && $isOwner,
            Response::HTTP_UNPROCESSABLE_ENTITY, 
            self::DEADLINE_HAS_EXPIRED
        );

        return $next($request);
    }
}
