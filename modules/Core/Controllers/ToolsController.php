<?php
namespace Modules\Core\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Modules\Core\Models\Settings;

class ToolsController extends Controller
{
    public function clearCache(Request $request)
    {
        $commands = [
            'cache:clear',
            'config:clear',
            'view:clear',
            'route:clear',
            'optimize:clear',
        ];

        foreach ($commands as $command) {
            Artisan::call($command);
        }

        $jsonResponse = $request->expectsJson()
            || $request->query('format') === 'json'
            || !auth()->check();

        if ($jsonResponse) {
            return response()->json([
                'status'  => 'success',
                'message' => __('Clear cache success!'),
                'cleared' => $commands,
            ]);
        }

        return redirect()->route('core.admin.tool.index')->with('success', __('Clear cache success!'));
    }
}
