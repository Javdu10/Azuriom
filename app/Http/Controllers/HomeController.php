<?php

namespace Azuriom\Http\Controllers;

use Azuriom\Models\Post;
use Azuriom\Models\Setting;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $posts = Post::published()
            ->with('author')
            ->latest('published_at')
            ->take(5)
            ->get();

        return view('home', ['posts' => $posts]);
    }

    public function maintenance(Request $request)
    {
        if (! setting('maintenance-status', false)) {
            return redirect()->home();
        }

        if ($request->user() !== null && $request->user()->can('maintenance.access')) {
            return redirect()->home();
        }

        $maintenanceMessage = setting('maintenance-message', trans('messages.maintenance-message'));

        return view('maintenance', ['maintenanceMessage' => $maintenanceMessage]);
    }

    public function setup(Request $request)
    {
        $settings = setting();
        $plugin = $request->input('plugin');
        abort_if($settings->has('core_installed'), 401);

        $manager = app('plugins');
        abort_unless(in_array($plugin, $manager->findPlugins()), 401);

        $manager->enable($plugin);
        Setting::updateSettings(['core_installed' => 1]);

        return redirect()->route('home');
    }
}
