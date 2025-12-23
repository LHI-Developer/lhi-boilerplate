<?php

namespace Modules\TestModule\Actions;

/**
 * ProcessDataAction
 * 
 * Single-purpose action for TestModule module.
 * 
 * Actions are reusable, single-responsibility operations
 * that can be called from controllers, services, or jobs.
 */
class ProcessDataAction
{
    /**
     * Execute the action.
     *
     * @return void
     */
    public function execute(): void
    {
        // TODO: Implement action logic here
        //
        // Best practices:
        // - One action = one specific operation
        // - Make it reusable across different contexts
        // - Use dependency injection if needed
        // - Keep it simple and focused
    }

    /**
     * Handle the action (alias for execute).
     *
     * @return void
     */
    public function handle(): void
    {
        $this->execute();
    }
}
