<?php
namespace Core\Interfaces;

/**
 * 
 * -------------------------------------------------------------------------------
 *  Run PHP
 * -------------------------------------------------------------------------------
 * - If we want to run php code inside html, normally we need 
 *   to open php tag, put our code, and close the tag: <?php //todo ?>
 * - Instead: we can use this template:
 * @example
 * @php
 *   $is_login = true;
 *   $user = getUser();
 * @endphp
 * -------------------------------------------------------------------------------
 * Statements
 * -------------------------------------------------------------------------------
 * - Blade {{ }} statements are automatically sent through PHP's 
 *   htmlspecialchars function to prevent XSS attacks.
 * @example
 * <h1> Hello {{user}} </h1>
 * -------------------------------------------------------------------------------
 * Conditions: 
 * -------------------------------------------------------------------------------
 * @example
 *  @if (statement)
 *       <!--html-->
 *  @elseif (statement)
 *       <!--html-->
 *  @else
 *       <!--html-->
 *  @endif
 * -------------------------------------------------------------------------------
 *  Loops Statement
 * -------------------------------------------------------------------------------
 * @example Foreach loop:
 * @foreach(items as item)
 *     <li>{{item}}</li>
 * @endforeach()
 * 
 * @example For loop:
 * @for ($i = 0; $i < 10; $i++)
 *      <li> {{ $i }} </li>
 * @endfor
 * 
 * @example While loop:
 * @while (true)
 *      <p>I'm looping..</p>
 *   @if (cond)
 *       @break
 *   @endif
 * @endwhile
 * 
 * -------------------------------------------------------------------------------
 *  Switch Statement
 * -------------------------------------------------------------------------------
 * Switch statements can be constructed using 
 * the @switch, @case, @break, @default and @endswitch directives:
 * 
 * @example
 * @switch($i)
 *      @case(1)
 *          First case...
 *          @break
 *  
 *      @case(2)
 *          Second case...
 *          @break
 *  
 *      @default
 *          Default case...
 *  @endswitch
 * 
 * -------------------------------------------------------------------------------
 *  Sessions
 * -------------------------------------------------------------------------------
 * - The @session directive may be used to determine if a session value exists.
 * - IF exists, we may want to echo its value using the var $value
 * @example
 *  @session('data')
 *      <div class="p-4 bg-green-100">
 *          {{ $value }}
 *      </div>
 *  @endsession
 * 
 * -------------------------------------------------------------------------------
 * HTTP Methods
 * -------------------------------------------------------------------------------
 * - As we now, html form only support GET and POST methods.
 * - To send additional methods like: PUT, UPDATE, PATCH, DELETE, etc, we use this:
 * @example
 *  <form action="/foo/bar" method="POST">
 *     @method('PUT')
 *     ...
 *</form>
 *
 * This will create a hidden file like this:
 * <input type='hidden' name='http_method' value='PUT'/>
 * 
 */
interface IBlade{

    
}