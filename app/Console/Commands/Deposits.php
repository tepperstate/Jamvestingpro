<?php

namespace App\Console\Commands;

use App\Models\Balance;
use App\Models\Deposit;
use App\Models\Referral;
use App\Notifications\DepositNotification;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use PrevailExcel\Nowpayments\Facades\Nowpayments;

class Deposits extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'daily:deposits';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'top up user fund after confirmation';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {

        $text = [
            'greeting' => 'Hello User',
            'subject' => 'Your Deposit was Successful',
            'body' => 'Your Deposit to our  wallet was approved and credited to your account, You can now start trading ',
            'data' => 'Click Here',
            'url' => url('/user'),
            'thanks' => 'Thank you for choosing'.env('APP_NAME'),
        ];

        $email = DB::table('admin_email')->where('id', 1)->first();

        $data = Deposit::where(['status' => 'waiting'])->get();

        if ($data) {
            foreach ($data as $value) {

                $status = Nowpayments::getPaymentStatus($value->payment_id);

                $statuss = $status['payment_status'];

                if ($statuss == 'finished') {
                    Balance::where(['user_id' => $value->user_id])->where('symbol', 'USD')->increment('amount', $value->amount);

                    Deposit::where(['trx_id' => $value->order_id])->update([
                        'status' => 'success',
                    ]);

                    $check_referral = Referral::where('referral_id', $value->user_id)->exists();   // for re

                    if ($check_referral) {
                        $datas = Referral::where('referral_id', $value->user_id)->first();   // for re

                        $user_id = $datas->user_id;
                        $ref_id = $datas->referral_id;

                        $divided_amount = (10 / 100) * $value->amount;

                        Balance::where('user_id', $user_id)->where('symbol', 'USD')->increment('referral', $divided_amount);
                        Balance::where('user_id', $user_id)->where('symbol', 'USD')->increment('amount', $divided_amount);

                        Referral::where('referral_id', $ref_id)->increment('balance', $divided_amount);
                    }

                    Notification::route('mail', $value->buyer_email)
                        ->notify(new DepositNotification($text));

                }

            }
        }

        return Command::SUCCESS;
    }
}
