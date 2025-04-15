<?php
namespace Core\Interfaces;

interface IApiStatus{
     /**
     * ---------------------------------------------------------------------------
     * Code 200+:  Request accepted
     * ---------------------------------------------------------------------------
     */
    // the request succeeded.
    public const _200_OK = 200; 
    // the request has succeeded and a new resource has been created as a result.
    public const _201_CREATED = 201;
    // The server accepted the request and will execute it later.
    public const _202_ACCEPTED = 202;
    // the request is accepted, but no required response body to return.
    // this used with methods PUT, POST, or DELETE.
    public const _204_NO_CONTENT = 404; 

    /**
     * ---------------------------------------------------------------------------
     * Code 300+: (Redirection)
     * ---------------------------------------------------------------------------
     */
    // the requested resource is changed permanently, new URL in header response.
    public const _301_MOVED_PERMANENTLY = 301; 
    // the requested resource is changed temporary, new URL in header response.
    public const _302_MOVED_TEMPORARY = 302;

    /**
     * ---------------------------------------------------------------------------
     * Code 400+: Client Error
     * ---------------------------------------------------------------------------
     */
    // the server could not understand the request due to incorrect syntax
    public const _400_BAD_REQUEST = 400;
    // the request requires user authentication information.
    public const _401_UNAUTHORIZED = 401;
    // the client does not have access rights to the content
    public const _403_FORBIDDEN = 403;
    // the server can not find the requested resource.
    public const _404_NOT_FOUND = 404;
    // the server knows the http method, but it has been disabled.
    public const _405_METHOD_NOT_ALLOWED = 405;
    // the server did not receive a complete request from the client within the server timeout period.
    public const _408_REQUEST_TIMEOUT = 408;
    // the server was unable to process the request because it contains invalid data
    public const _422_UNPROCESSABLE = 422;
    // the request could not be completed due to a conflict with the current state of the resource.
    public const _409_CONFLICT = 409;
    // the request uri is langer that the server can interpret.
    public const _414_URI_TOO_LONG = 414;
    // the media type in Content-type of the request is not supported by the server.
    public const _415_UNSUPPORTED_MEDIA_TYPE = 415; 
    // the client need to upgrade to a different protocol.
    public const _426_UPGRADE_REQUIRED = 426;
    // The client has sent too many requests in a given amount of time
    public const _429_RATE_LIMIT = 429;
    // the client closes the connection while server is processing its request.
    public const _499_CLIENT_CLOSED = 499;

    /**
     * ---------------------------------------------------------------------------
     * Code 500+: Server Error
     * ---------------------------------------------------------------------------
     * the client has no control todo with these errors. its server issues 
     */
    // the server get unexpected condition that prevented it from handling the request.
    public const _500_INTERNAL_ERROR = 500;
    // The HTTP method is not supported by the server and cannot be handled.
    public const _501_NOT_IMPLEMENTED = 501;
    // the server is acting as a gateway or proxy, 
    // and it received an invalid response from the upstream server.
    public const _502_BAD_GATEWAY = 502;
    // the server is not ready to handle the request.
    public const _503_SERVICE_UNAVAILABLE = 503;
    // The server is acting as a gateway and cannot get a response in time for a request.
    public const _504_GATEWAY_TIMEOUT = 504;
    // Http version used in request is not supported by the server.
    public const _505_BAD_HTTP_VERSION = 505;
}
