<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactSubmission;
use Illuminate\Http\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Illuminate\View\View;

class ContactSubmissionAdminController extends Controller
{
    public function index(): View
    {
        $submissions = ContactSubmission::query()
            ->latest('id')
            ->paginate(50);

        return view('admin.contact-submissions.index', [
            'submissions' => $submissions,
        ]);
    }

    public function export(): StreamedResponse
    {
        $filename = 'contact-submissions-'.now()->format('Ymd-His').'.csv';

        return response()->streamDownload(function (): void {
            $handle = fopen('php://output', 'w');

            if ($handle === false) {
                return;
            }

            fputcsv($handle, [
                'id',
                'first_name',
                'last_name',
                'email',
                'phone',
                'message',
                'ip_address',
                'user_agent',
                'created_at',
            ]);

            ContactSubmission::query()
                ->orderBy('id')
                ->chunk(500, function ($items) use ($handle): void {
                    foreach ($items as $item) {
                        fputcsv($handle, [
                            $item->id,
                            $item->first_name,
                            $item->last_name,
                            $item->email,
                            $item->phone,
                            $item->message,
                            $item->ip_address,
                            $item->user_agent,
                            optional($item->created_at)->toDateTimeString(),
                        ]);
                    }
                });

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }
}
