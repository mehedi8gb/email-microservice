<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreEmailRequest;
use App\Http\Requests\UpdateEmailRequest;
use App\Models\Email;

class EmailController extends Controller
{
    public function index($company_id)
    {
        return Email::where('company_id', $company_id)->get();
    }

    public function send(StoreEmailRequest $request, $company_id)
    {
        $email = Email::create(array_merge($request->validated(), ['company_id' => $company_id]));
        return response()->json(['message' => 'Email stored', 'email' => $email]);
    }

    public function update(UpdateEmailRequest $request, Email $email)
    {
        $email->update($request->validated());
        return $email;
    }
}
