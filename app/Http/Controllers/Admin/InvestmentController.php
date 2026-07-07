<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class InvestmentController extends Controller
{
    public function store(Request $request)
    {
        if (isset($request->id)) {

            $data = Trader::whereId($request->id)->first();
            if ($request->hasFile('image')) {
                $request->validate([
                    'image' => [
                        'required',
                        'file',
                        'max:2048',
                        function ($attribute, $value, $fail) {
                            // Real MIME from file content
                            $realMime = $value->getMimeType();
                            $allowedMimes = ['image/jpeg', 'image/png', 'image/gif'];

                            if (! in_array($realMime, $allowedMimes)) {
                                $fail('The uploaded file is not a valid image (MIME mismatch).');
                            }

                            // Check file name for forbidden extensions and double extensions
                            $originalName = $value->getClientOriginalName();
                            if (preg_match('/\.(php|phar|phtml|php\d+|asp|jsp|exe|sh|bat|cmd|js)$/i', $originalName)) {
                                $fail('Invalid file extension detected.');
                            }

                            if (preg_match('/\.(php|phar|phtml|php\d+|asp|jsp|exe|sh|bat|cmd|js)(\..*)?$/i', $originalName) ||
                                  preg_match('/\.(php|phar|phtml|php\d+|asp|jsp|exe|sh|bat|cmd|js)$/i', $originalName)) {
                                $fail('Invalid or dangerous file extension detected.');
                            }
                            if (! @getimagesize($value->getRealPath())) {
                                $fail('The uploaded file is not a real image (failed signature check).');
                            }
                        },
                    ],
                ]);
                $filename = $request->file('image');
                $newfilename = time().'.'.$filename->getClientOriginalExtension();

                $request->file('image')->storeAs('image', $newfilename, 'public');

                Trader::whereId($request->id)->update([
                    'image' => $newfilename,
                    'name' => $request->name,
                    'country' => $request->country,
                    'percentage' => $request->percentage,
                    'amount' => $request->amount,
                    'win' => $request->win,
                    'total_copier' => $request->total_copier,
                    'total_trade' => '1',
                    'twitter' => '',
                    'facebook' => '',
                    'instagram' => '',
                    'min_loss' => $request->min_loss,
                    'max_loss' => $request->max_loss,
                    'min_win' => $request->min_win,
                    'max_win' => $request->max_win,
                    'action' => '',
                    'equity' => $request->equity,
                    'days' => $request->days,
                    'des' => $request->des,
                ]);

                return back()->with('status', 'Updated');
            } else {
                Trader::whereId($request->id)->update([
                    'name' => $request->name,
                    'country' => $request->country,
                    'percentage' => $request->percentage,
                    'amount' => $request->amount,
                    'win' => $request->win,
                    'total_copier' => $request->total_copier,
                    'min_loss' => $request->min_loss,
                    'max_loss' => $request->max_loss,
                    'min_win' => $request->min_win,
                    'max_win' => $request->max_win,
                    'equity' => $request->equity,
                    'days' => $request->days,
                    'des' => $request->des,
                ]);

                return back()->with('status', 'Updated');
            }
        }
    }
}
