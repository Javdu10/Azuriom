<?php

namespace Azuriom\Http\Controllers\Admin;

use Azuriom\Http\Controllers\Controller;
use Azuriom\Http\Requests\ServerRequest;
use Azuriom\Models\Server;
use Azuriom\Models\Setting;
use Exception;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use RuntimeException;

class ServerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('admin.servers.index', [
            'servers' => Server::with('stat')->get(),
            'defaultServerId' => (int) setting('default-server'),
        ]);
    }

    /**
     * Change the default server.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function changeDefault(Request $request)
    {
        $this->validate($request, [
            'server' => ['nullable', Rule::exists('servers', 'id')],
        ]);

        Setting::updateSettings('default-server', $request->input('server'));

        return redirect()->route('admin.servers.index')->with('success', trans('admin.servers.status.updated'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view(
            game()->getServerCreateView(), 
            ['types' => Server::types()]
        );
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \Azuriom\Models\Server  $server
     * @return \Illuminate\Http\Response
     */
    public function edit(Server $server)
    {
        return view(
            game()->getServerEditView(), [
            'server' => $server,
            'types' => Server::types(),
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \Azuriom\Models\Server  $server
     * @return \Illuminate\Http\Response
     *
     * @throws \Exception
     */
    public function destroy(Server $server)
    {
        $server->delete();

        return redirect()->route('admin.servers.index')->with('success', trans('admin.servers.status.deleted'));
    }
}
