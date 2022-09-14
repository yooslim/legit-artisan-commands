<?php

namespace YOoSlim\LegitArtisanCommands\Console\Commands;

use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Validator;
use YOoSlim\LegitArtisanCommands\Models\ConsoleToken;
use YOoSlim\LegitArtisanCommands\Utils\ArtisanAuthenticationHandler;

class ArtisanAuthenticationCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'console:authentication
                            {--username= : Username }
                            {--revoke : Revoke old user tokens }';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Lets you authenticate a user using provided username and password.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $validator = Validator::make([
            'username' => $this->option('username'),
            'revoke' => $this->option('revoke', false)
        ], [
            'username' => 'required|string',
            'revoke' => 'required|boolean'
        ]);

        if ($validator->fails()) {
            foreach ($validator->errors()->messages() as $field => $messages) {
                $this->error($field . ': ' . implode(', ', $messages));
            }
        } else {
            $data = $validator->getData();

            $password = $this->secret(trans('legit-artisan-commands::messages.what_is_your_password'));

            if (empty($password)) $this->warn(trans('legit-artisan-commands::messages.make_sure_you_provide_a_valid_password'));
            else {
                try {
                    $user = ArtisanAuthenticationHandler::authenticateUser($data['username'], $password);

                    if (!is_null($user)) {
                        // Revoke old user tokens
                        if ($data['revoke']) {
                            ConsoleToken::where('user_id', $user->getUserId())->delete();
                            $this->info(trans('legit-artisan-commands::messages.old_tokens_revoked'));
                        }

                        // Create token
                        $token = new ConsoleToken();
                        $token->user()->associate($user->getUserId());
                        $token->save();

                        $this->info(trans('legit-artisan-commands::messages.token_has_been_generated'));
                        $this->info('Token: ' . $token->value);
                    } else {
                        $this->warn(trans('legit-artisan-commands::messages.user_authentication_failed')); 
                    }
                } catch (Exception $e) {
                    $this->warn(trans('legit-artisan-commands::messages.user_authentication_failed_see_logs'));

                    if ($this->option('verbose', false)) {
                        $this->error($e->getMessage());
                    }
                }
            }
        }
    }
}
