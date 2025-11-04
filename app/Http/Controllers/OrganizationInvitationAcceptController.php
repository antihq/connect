<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\OrganizationInvitation;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;

class OrganizationInvitationAcceptController extends Controller
{
    public function __invoke(OrganizationInvitation $invitation): RedirectResponse
    {
        $user = Auth::user();
        $organization = $invitation->organization;

        $organization->addMember($user);
        $invitation->delete();
        $user->switchOrganization($organization);

        return redirect()->route('dashboard');
    }
}
