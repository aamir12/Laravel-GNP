<?php

namespace App\Http\Requests\League;

use Illuminate\Foundation\Http\FormRequest;
use Auth;

class InviteUserToLeagueRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */

    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'user_id' => 'required|
                          exists:users,id|
                          unique:league_invites,invitee_id,NULL,id,league_id,' . $this->league_id,
            'league_id' => 'required|
                            exists:leagues,id,deleted_at,NULL,type,Private,owner_id,' . Auth::id()
        ];
    }
}
