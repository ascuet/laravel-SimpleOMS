<?php namespace App\Commands;

use App\Commands\Command;
use App\Services\LogService;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Bus\SelfHandling;
use Illuminate\Contracts\Queue\ShouldBeQueued;

class WriteLog extends Command implements SelfHandling, ShouldBeQueued {

	use InteractsWithQueue, SerializesModels;
	protected $data;
	/**
	 * Create a new command instance.
	 *
	 * @return void
	 */
	public function __construct($data)
	{
		//
		$this->data = $data;
	}

	/**
	 * Execute the command.
	 *
	 * @return void
	 */
	public function handle(LogService $service)
	{
		//
		$service->create($this->data);
	}

}
