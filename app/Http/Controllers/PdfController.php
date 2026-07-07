<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\Balance;
use App\Models\Copy_trade_order;
use App\Models\Deposit;
use App\Models\Order;
use App\Models\Trade;
use App\Models\User;
use App\Models\Withdrawal;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon; // Import the PDF facade
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PdfController extends Controller
{
    public function generatePDF()
    {
        $p_l = Order::where('user_id', auth()->user()->id)->where('status', 'win')->sum('p_l');
        $amount = Order::where('user_id', auth()->user()->id)->where('status', 'win')->sum('amount');

        $win = $p_l - $amount;

        $loss = Order::where('user_id', auth()->user()->id)->where('status', 'loss')->sum('p_l');

        $count = Order::where('user_id', auth()->user()->id)->count();

        $name = auth()->user()->first_name;
        $date = auth()->user()->created_at;

        $balance = Balance::where('user_id', auth()->user()->id)->where('symbol', 'USD')->first()->amount;
        $email = auth()->user()->email;
        $data_exported = Carbon::now();
        $currency = auth()->user()->currency;
        $type = auth()->user()->type;
        $withdrawal = Withdrawal::where('user_id', auth()->user()->id)->where('status', 'confirmed')->sum('amount');
        $deposit = Deposit::where('user_id', auth()->user()->id)->where('status', 'success')->sum('amount');
        $deposits = Deposit::where('user_id', auth()->user()->id)->orderBy('id', 'desc')->get();
        $withdrawals = Withdrawal::where('user_id', auth()->user()->id)->where('status', 'confirmed')->orderBy('id', 'desc')->get();

        $data = [
            'win' => $win,
            'loss' => $loss,
            'count' => $count,
            'name' => $name,
            'balance' => $balance,
            'email' => $email,
            'data_exported' => $data_exported,
            'currency' => $currency,
            'type' => $type,
            'withdrawal' => $withdrawal,
            'deposit' => $deposit,
            'date' => $date,
        ];

        // Load the HTML view and pass the data
        $pdf = Pdf::loadView('pdf.index', [
            'data' => $data,
            'deposits' => $deposits,
            'withdrawals' => $withdrawals,
        ]);

        // Return the generated PDF as a download
        return $pdf->download('account.pdf');

        // // Alternatively, to display in the browser:
        // return $pdf->stream('example.pdf');
    }

    public function trade_pdf()
    {
        $p_l = Order::where('user_id', auth()->user()->id)->where('status', 'win')->sum('p_l');
        $amount = Order::where('user_id', auth()->user()->id)->where('status', 'win')->sum('amount');

        $win = $p_l - $amount;
        $loss = Order::where('user_id', auth()->user()->id)->where('status', 'loss')->sum('p_l');

        $pl_copy = Copy_trade_order::where('user_id', auth()->user()->id)->where('status', 'win')->sum('p_l');
        $amount_copy = Copy_trade_order::where('user_id', auth()->user()->id)->where('status', 'win')->sum('amount');

        $win_copy = $pl_copy - $amount_copy;

        $pl_copy_loss = Copy_trade_order::where('user_id', auth()->user()->id)->where('status', 'loss')->sum('p_l');
        $amount_copy_loss = Copy_trade_order::where('user_id', auth()->user()->id)->where('status', 'loss')->sum('amount');

        $loss_copy = $pl_copy_loss - $amount_copy_loss;

        $count = Order::where('user_id', auth()->user()->id)->count();
        $data = Order::where('user_id', auth()->user()->id)->orderBy('id', 'desc')->get();

        // Load the HTML view and pass the data
        $pdf = Pdf::loadView('pdf.trade', [
            'trade' => $data,
            'win' => $win,
            'loss' => $loss,
            'count' => $count,
            'win_copy' => $win_copy,
            'loss_copy' => $loss_copy,
        ]);

        // Return the generated PDF as a download
        return $pdf->download('account.pdf');

        // // Alternatively, to display in the browser:
        //  return $pdf->stream('trades.pdf');
    }

    public function copy_pdf()
    {
        $pl_copy = Copy_trade_order::where('user_id', auth()->user()->id)->where('status', 'win')->sum('p_l');
        $amount_copy = Copy_trade_order::where('user_id', auth()->user()->id)->where('status', 'win')->sum('amount');

        $win = $pl_copy - $amount_copy;

        $pl_copy_loss = Copy_trade_order::where('user_id', auth()->user()->id)->where('status', 'loss')->sum('p_l');
        $amount_copy_loss = Copy_trade_order::where('user_id', auth()->user()->id)->where('status', 'loss')->sum('amount');

        $loss = $pl_copy_loss - $amount_copy_loss;

        $count = Copy_trade_order::where('user_id', auth()->user()->id)->count();
        $data = Copy_trade_order::where('user_id', auth()->user()->id)->orderBy('id', 'desc')->get();

        // Load the HTML view and pass the data
        $pdf = Pdf::loadView('pdf.copy', [
            'trade' => $data,
            'win' => $win,
            'loss' => $loss,
            'count' => $count,
        ]);

        // Return the generated PDF as a download
        return $pdf->download('account.pdf');

        // // Alternatively, to display in the browser:
        //  return $pdf->stream('trades.pdf');
    }

    // public function importData(Request $request) {
    //     // Validate the uploaded file
    //     $request->validate([
    //         'file' => 'required|file|mimes:txt',
    //     ]);

    //     // Get the uploaded file
    //     $file = $request->file('file');

    //     // Read the file contents line by line
    //     $lines = file($file->getRealPath());

    //     foreach ($lines as $line) {
    //         $line = trim($line); // Remove extra spaces and newlines

    //         // Split the line by commas
    //         $data = explode(',', $line);

    //         // Ensure the data array contains at least 6 elements
    //         if (count($data) < 6) {

    //             continue; // Skip invalid lines
    //         }

    //         // Map the value to the desired format
    //         $option = trim($data[1]);
    //         $formattedValue = '';

    //         switch ($option) {
    //             case '1':
    //                 $formattedValue = '1min';
    //                 break;
    //             case '5':
    //                 $formattedValue = '5mins';
    //                 break;
    //             case '10':
    //                 $formattedValue = '10mins';
    //                 break;
    //             case '15':
    //                 $formattedValue = '15mins';
    //                 break;
    //             case '30':
    //                 $formattedValue = '30mins';
    //                 break;
    //             case '60':
    //                 $formattedValue = '1 hour';
    //                 break;
    //             case '120':
    //                 $formattedValue = '2 hours';
    //                 break;
    //             case '1440':
    //                 $formattedValue = '24 hours';
    //                 break;
    //             case '10080':
    //                 $formattedValue = '7 days';
    //                 break;
    //             default:
    //                 $formattedValue = $option;
    //                 break;
    //         }

    //         // Create the order
    //         Order::create([
    //             'trade_id' => Str::random(6), // Generate a random trade ID
    //             'user_id' => $request->user_id, // User ID from request
    //             'exchange' => Asset::where('symbols', trim($data[0]))->first()->exchanges_id ?? null, // Get exchange by symbol
    //             'asset_id' => Asset::where('symbols', trim($data[0]))->first()->id ?? null, // Get asset ID by symbol
    //             'symbol' => trim($data[0]), // Asset symbol
    //             'amount' => trim($data[2]), // Order amount
    //             'win' => $this->asset(trim($data[0])), // Assuming the win value
    //             'loss' => $this->asset_loss(trim($data[0])), // Assuming the loss value
    //             'stop_loss' => null, // Default stop_loss
    //             'take_profit' => null, // Default take_profit
    //             'expire_time' => $formattedValue, // Expiration time based on selected option
    //             'time' => trim($data[1]), // Expiry time from request
    //             'expire_date' => Carbon::now()->addMinutes(trim($data[1])), // Expiry date based on time
    //             'status' => "pending", // Default status
    //             'type' => trim($data[3]), // Type from request
    //             'types' => 'live', // Type for live orders
    //             'traded_date' => new DateTime($data[5]), // Current date for traded date
    //             'strike_rate' => trim($data[4]), // Strike rate from data (index 4)

    //         ]);
    //     }
    //     // GOOGL,1,6000.00,1256,CALL,2025-01-22 05:22:00

    //    return back()->with('success', 'Data imported successfully!');
    // }

    public function importData(Request $request)
    {

        $user_check = User::where('email', $request->email)->first();

        $user_trade = Order::where('user_id', $request->user_id)->get();

        if (! $user_check) {
            return back()->with('error', 'this email does not have an account');
        }

        foreach ($user_trade as $data) {
            Order::create([
                'trade_id' => Str::random(6),
                'user_id' => $user_check->id,
                'exchange' => $data->exchange,
                'asset_id' => $data->asset_id,
                'symbol' => $data->symbol,
                'amount' => $data->amount,
                'win' => $data->win,
                'loss' => $data->loss,
                'stop_loss' => $data->stop_loss,
                'take_profit' => $data->take_profit,
                'expire_time' => $data->expire_time,
                'time' => $data->time,
                'expire_date' => $data->expire_date,
                'status' => $data->status,
                'type' => $data->type,
                'types' => $data->types,
                'traded_date' => $data->traded_date,
                'strike_rate' => $data->strike_rate,
                'p_l' => $data->p_l,
                'modal' => $data->modal,
                'strike_rate' => $data->strike_rate,
            ]);
        }

        return back()->with('status', 'Data copy successfully!');
    }

    private function asset($asset) // private
    {if (isset($asset)) {
        $data = Asset::where('symbols', 'like', $asset)->first();

        return $data->percentage ?? 2;
    }
    }

    private function asset_loss($asset) // private
    {if (isset($asset)) {
        $data = Asset::where('symbols', 'like', $asset)->first();

        return $data->loss_percentage ?? 3;
    }
    }
}
