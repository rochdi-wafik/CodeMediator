<?php 
namespace Core\Interfaces;

interface IDatabase{
    public const FLAG_FETCH_FIRST  = 10; // "ORDER BY id ASC LIMIT 1";
	public const FLAG_UPDATE_FIRST = 11; // "ORDER BY id ASC LIMIT 1";
	public const FLAG_REMOVE_FIRST = 12; // "ORDER BY id ASC LIMIT 1";

	public const FLAG_FETCH_LAST   = 13; // "ORDER BY id DESC LIMIT 1";
	public const FLAG_UPDATE_LAST  = 14; // "ORDER BY id DESC LIMIT 1";
	public const FLAG_REMOVE_LAST  = 15; // "ORDER BY id DESC LIMIT 1";

	public const FLAG_FETCH_ALL    = 16; // "FETCH_ALL";
	public const FLAG_FETCH_SINGLE = 17; // "FETCH";
	public const FLAG_FETCH        = 17; // "FETCH";
	public const FLAG_COUNT        = 18; // "COUNT";
	public const FLAG_EXECUTE      = 19; // "EXECUTE";
}