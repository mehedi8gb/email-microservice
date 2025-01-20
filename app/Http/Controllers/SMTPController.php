<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSMTPRequest;
use App\Http\Requests\UpdateSMTPRequest;
use App\Models\SMTPConfig;

class SMTPController extends Controller
{
    public function store(StoreSMTPRequest $request, $company_id)
    {
        return SMTPConfig::updateOrCreate(['company_id' => $company_id], $request->validated());
    }

    public function update(UpdateSMTPRequest $request, SMTPConfig $smtp): SMTPConfig
    {
        $smtp->update($request->validated());
        return $smtp;
    }


    public function show($company_id)
    {
        return SMTPConfig::where('company_id', $company_id)->firstOrFail();
    }
}

