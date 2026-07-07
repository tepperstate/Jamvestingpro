<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Email;
use App\Models\Noti;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MessageController extends Controller
{
    public function custom_message(Request $request)
    {
        $request->validate([
            'user' => 'required',
            'custom_header' => 'nullable|string|max:255',
            'custom_message' => 'nullable|string',
        ]);

        User::whereId($request->user)->update([
            'custom_header' => $request->custom_header,
            'custom_message' => $request->custom_message,
        ]);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Interface message matrix updated successfully.',
            ]);
        }

        return back()->with('status', 'custom message updated');
    }

    public function delete_message($id)
    {
        Email::where('id', $id)->delete();

        return back()->with('status', 'message deleted');
    }

    public function bank_error_message(Request $request)
    {
        DB::table('site_settings')->where('id', 1)->update([
            'withdrawal_message' => $request->withdrawal_message,
        ]);

        return back()->with('status', 'bank error message updated');
    }

    public function user_lock(Request $request)
    {
        DB::table('lock_message')->where('id', 1)->update([
            'title' => $request->title,
            'message' => $request->message,
        ]);

        return back()->with('status', 'lock messages  updated');
    }

    public function system_message(Request $request)
    {
        DB::table('system_message')->where('id', 1)->update([
            'message' => $request->message,
        ]);

        return back()->with('status', 'system message updated');
    }

    public function deposit_message(Request $request) // depposit message
    {$data = DB::table('deposit_message')->where('user_id', $request->user_id)->get();

        if (count($data) < 1) {
            DB::table('deposit_message')->insert([
                'user_id' => $request->user_id,
                'message' => $request->message,
            ]);
        } else {
            DB::table('deposit_message')->where('user_id', $request->user_id)->update([
                'message' => $request->message,
            ]);
        }

        return back()->with('status', 'deposit message updated');
    }

    public function withdrawal_message(Request $request) // withdrawal message
    {$data = DB::table('withdrawal_message')->where('user_id', $request->user_id)->get();

        if (count($data) < 1) {
            DB::table('withdrawal_message')->insert([
                'user_id' => $request->user_id,
                'message' => $request->message,
            ]);
        } else {
            DB::table('withdrawal_message')->where('user_id', $request->user_id)->update([
                'message' => $request->message,
            ]);
        }

        return back()->with('status', 'withdrawal message updated');
    }

    public function delete_withdrawal_message($id) // / delete withdrawal message
    {DB::table('withdrawal_message')->where('id', $id)->delete();

        return back()->with('status', 'withdrawal message deleted');

    }

    public function delete_deposit_message($id) // delete deposit message
    {DB::table('deposit_message')->where('id', $id)->delete();

        return back()->with('status', 'deposit message deleted');
    }

    public function send_message(Request $request) // send message
    {$email = Email::create([
           'user_id' => $request->user_id,
           'to' => 'Admin',
           'sent_to' => 'inbox',
           'subject' => $request->subject,
           'message' => $request->message,
           'status' => 'unread',
           'thread_id' => $request->thread_id,
           'reply_to_id' => $request->reply_to_id,
       ]);

        if (empty($request->thread_id)) {
            $email->thread_id = $email->id;
            $email->save();
        }

        // Notify the user so they see the message in their notification center
        Noti::create([
            'user_id' => $request->user_id,
            'message' => 'You have a new message from Admin: "'.$request->subject.'". Check your Inbox.',
            'status' => 'unread',
        ]);

        return back()->with('status', 'Message Sent');
    }

    public function send_notification(Request $request) // send message
    {Noti::create([
            'user_id' => $request->user_id,
            'message' => $request->message,
            'status' => 'unread',
        ]);

        return back()->with('status', 'Message Sent');
    }

    public function single_message($id, $user_id) // getsingle message
    {$user = User::whereId($user_id)->first();
        $email = Email::whereId($id)->first();

        $thread = Email::where('thread_id', $email->thread_id ?? $email->id)
            ->orderBy('created_at', 'asc')
            ->get();

        return view('admin.user_message', [
            'user' => $user,
            'email' => $email,
            'thread' => $thread,
        ]);
    }

    public function update_wecome_message(Request $request) // update welome message
    {DB::table('messages')->where('id', 1)->update([
            'message' => $request->message,
        ]);

        return back()->with('status','welcome message updated');
    }
}
