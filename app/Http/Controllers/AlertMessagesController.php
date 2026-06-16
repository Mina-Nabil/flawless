<?php

namespace App\Http\Controllers;

use App\Models\AlertMessage;
use App\Models\AlertRecipient;
use App\Models\DashUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AlertMessagesController extends Controller
{
    /**
     * Owner management page: create alerts and review read confirmations.
     */
    public function index()
    {
        if (!Auth::user()->isOwner()) {
            abort(403);
        }

        $this->data['title'] = "Alert Messages";
        $this->data['formTitle'] = "Leave an Alert";
        $this->data['formSubtitle'] = "Send a moving alert to selected users and track who confirmed reading it";
        $this->data['alerts'] = AlertMessage::with('creator', 'recipientRows.user')->orderByDesc('id')->get();
        $this->data['users'] = DashUser::with('dash_types')
            ->where('id', '!=', Auth::id())
            ->where('DASH_ACTV', 1)
            ->orderBy('DASH_USNM')
            ->get()
            ->groupBy(fn($user) => ucfirst($user->dash_types->DHTP_NAME ?? 'Other'));
        $this->data['addAlertURL'] = url('alerts/add');

        return view('settings.alerts', $this->data);
    }

    /**
     * Create a new alert and attach the chosen recipients (unread).
     */
    public function store(Request $request)
    {
        if (!Auth::user()->isOwner()) {
            abort(403);
        }

        $request->validate([
            "message"       => "required|string",
            "recipients"    => "required|array|min:1",
            "recipients.*"  => "exists:dash_users,id",
            "expiry"        => "nullable|date",
        ]);

        $alert = AlertMessage::create([
            "ALRT_TEXT"     => $request->message,
            "ALRT_DASH_ID"  => Auth::id(),
            "ALRT_ACTV"     => 1,
            "ALRT_EXPR"     => $request->expiry ?: null,
        ]);

        foreach (array_unique($request->recipients) as $userID) {
            AlertRecipient::create([
                "ALRC_ALRT_ID"  => $alert->id,
                "ALRC_DASH_ID"  => $userID,
                "ALRC_READ_AT"  => null,
            ]);
        }

        return back();
    }

    /**
     * Activate / deactivate an alert.
     */
    public function toggle($id)
    {
        if (!Auth::user()->isOwner()) {
            abort(403);
        }

        $alert = AlertMessage::findOrFail($id);
        $alert->ALRT_ACTV = !$alert->ALRT_ACTV;
        $alert->save();

        return back();
    }

    public function delete($id)
    {
        if (!Auth::user()->isOwner()) {
            abort(403);
        }

        AlertMessage::findOrFail($id)->delete();

        return back();
    }

    /**
     * Current user confirms they read an alert. Available to every authenticated user.
     */
    public function confirmRead(Request $request)
    {
        $request->validate([
            "id" => "required|exists:alert_messages,id",
        ]);

        $recipient = AlertRecipient::where('ALRC_ALRT_ID', $request->id)
            ->where('ALRC_DASH_ID', Auth::id())
            ->firstOrFail();

        if ($recipient->ALRC_READ_AT == null) {
            $recipient->ALRC_READ_AT = now();
            $recipient->save();
        }

        return response()->json(["success" => true]);
    }
}
