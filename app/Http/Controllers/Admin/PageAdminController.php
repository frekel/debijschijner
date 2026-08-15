<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Page;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PageAdminController extends Controller
{
    public function index(): View
    {
        $pages = Page::query()
            ->orderByRaw("CASE WHEN slug = 'home' THEN 0 ELSE 1 END")
            ->orderBy('slug')
            ->get();

        return view('admin.pages.index', [
            'pages' => $pages,
        ]);
    }

    public function create(): View
    {
        return view('admin.pages.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'slug' => ['required', 'string', 'max:255', 'unique:pages,slug'],
            'title' => ['required', 'string', 'max:255'],
            'html' => ['required', 'string'],
            'is_published' => ['nullable', 'boolean'],
        ]);

        $data['slug'] = trim($data['slug'], '/');
        $data['is_published'] = (bool) ($data['is_published'] ?? false);

        Page::create($data);

        return redirect()
            ->route('admin.pages.index')
            ->with('status', 'Pagina aangemaakt.');
    }

    public function edit(Page $page): View
    {
        return view('admin.pages.edit', [
            'page' => $page,
        ]);
    }

    public function update(Request $request, Page $page): RedirectResponse
    {
        $data = $request->validate([
            'slug' => ['required', 'string', 'max:255', 'unique:pages,slug,'.$page->id],
            'title' => ['required', 'string', 'max:255'],
            'html' => ['required', 'string'],
            'is_published' => ['nullable', 'boolean'],
        ]);

        $data['slug'] = trim($data['slug'], '/');
        $data['is_published'] = (bool) ($data['is_published'] ?? false);

        $page->update($data);

        return redirect()
            ->route('admin.pages.index')
            ->with('status', 'Pagina bijgewerkt.');
    }

    public function destroy(Page $page): RedirectResponse
    {
        $page->delete();

        return redirect()
            ->route('admin.pages.index')
            ->with('status', 'Pagina verwijderd.');
    }
}
