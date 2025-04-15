<?php 
namespace Core\Interfaces;

interface IMiddleware{
    /**
     * Handle
     * -------------------------------------------------------
     */
    public function handle(): ?bool;
}