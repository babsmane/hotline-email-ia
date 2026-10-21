<?php

namespace App\Console\Commands;

use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use App\Models\InboundEmail;
use App\Services\EmailClassifier;

#[Signature('app:classify-pending-emails')]
#[Description('Command description')]
class ClassifyPendingEmails extends Command
{
    /**
     * Execute the console command.
     */
    protected $signature = 'emails:classify-pending {--limit=50}';
        protected $description = 'Classe les e-mails en attente ou en échec';

        public function handle(EmailClassifier $classifier): int
        {
            $emails = InboundEmail::whereIn('status', ['pending', 'failed'])
                ->limit((int) $this->option('limit'))
                ->get();

            foreach ($emails as $email) {
                $result = $classifier->classify($email);
                $this->line("#{$result->id} [{$result->status}] {$result->category} / {$result->priority}"
                    . ($result->needs_human ? ' (humain)' : ''));
            }

            return self::SUCCESS;
        }
}
