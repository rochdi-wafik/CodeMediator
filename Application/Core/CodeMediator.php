<?php
namespace Core;

class CodeMediator{

    // Show alert message
    public static function show_msg($msg = null,  $type = null, $legend = null)
    {
        if($msg == null){
            return null;
        }
        
        $border_color = null;
        $bg_color = null;
        
        switch ($type) {
            case SUCCESS:
                $border_color = "#4caf50";
                $bg_color = "#deeade";
                break;
            case WARNING:
                $border_color = "#f3a800";
                $bg_color = "#f3a8002b";
                break;
            case DANGER:
                $border_color = "#dc3545";
                $bg_color = "#f7dfe1";
                break;
            case PRIMARY:
                $border_color = "#007bff";
                $bg_color = "#d7e9fc";
                break;
            case SECONDARY:
                $border_color = "#6c757d";
                $bg_color = "#e7e8e9";
                break;
            case INFO:
                $border_color = "#03a9f4";
                $bg_color = "#daeff2";
                break;
            default:
                $border_color = "#007bff";
                $bg_color = "#007bff25";
                break;
        }
        $direction = (verify_config('language','arabic')) ? 'right' :'left';
        // $direction = is_devmode() ? 'left' : $direction;

        $stylesheet = "
            font-size: 15px;
            color: #555;
            line-height: 28px;
            width: 100%;
            padding: 1rem;
            margin: 0.3rem 0;
            display: flex;
            justify-content: flex-start;
            align-items: center;
            background-color: {$bg_color};
            border-{$direction}: 3px solid ;
            border-color: {$border_color};
        ";
        
        $legend = ($legend != null ) ? $legend.' :' : null;

        /**/
        $div = '<style>html,*{padding:0;margin:0;box-sizing:border-box}.show_msg a:not([href]), .show_msg a{color:#0a9adb;padding:0 4px}</style>
        <div class="show_msg" style="'.$stylesheet.'">
        <b style="color:'.$border_color.'">'.$legend.'&nbsp;</b> '.$msg.'</div>';

        if(is_devmode())
        {
            die($div);
        }
        return $div;

    }

    public static function log($title='General log', string $content="", $description = null){
        $logfile = @fopen(BASE.DS.'framework-log.log', 'a');
        $txt = "\n#log-on: ".date("Y/m/d  H:i"). " --------------------------------------------------\n";
        $txt .= "[{$title}] \n";
        $txt .= "[-] ".strip_tags($content)." \n";
        if($description != null)
        {
            $txt .= "[-] ".strip_tags($description)." \n";
        }
        $txt .= "[-] Tracerout: ".tracerout_pure(false)."\n";
        $txt .= "[-] Page affected : ".current_url() ."\n";
        @fwrite($logfile, $txt);
        @fclose($logfile);
        
    }

    // Show box Error
    public static function show_error($info = null)
    {
        //tracerout();
        
        if(is_array($info))
        {
            extract($info);
            $_title = isset($title) ? $title : "CodeMediator Found Errors";
            $_content = isset($content) ? $content : null;
            $_level = isset($level) ? $level : DANGER;
            $_description = isset($description) ? $description : null;
            $_track = isset($track) ? $track : null;
        }
        else{
            $_content = $info;
        }

        // flash error in framework.log

        // $logfile = @fopen(BASE.DS.'framework-log.log', 'a');
        // $txt = "\n#log-on: ".date("Y/m/d  H:i"). " --------------------------------------------------\n";
        // $txt .= "[{$_title}] \n";
        // $txt .= "[-] ".strip_tags($_content)." \n";
        // if($_description != null)
        // {
        //     $txt .= "[-] ".strip_tags($_description)." \n";
        // }
        // $txt .= "[-] Tracerout: ".cut_from_end(tracerout_pure(false), "[=] CodeMediator::show_error()")."\n";
        // $txt .= "[-] Page affected : ".current_url() ."\n";
        // @fwrite($logfile, $txt);
        // @fclose($logfile);
        



        $style = "<style>.cm_error *{direction: ltr!important;text-align:left!important;}a,u,b,i{padding:2px}.cm_error fieldset{padding: 1rem;margin: 1rem;border: 2px solid #dc3545;background: #fbfbfbad;}.cm_error legend{font-size:1rem;padding: 0.5rem;color:#fff;background: #dc3545;font-family: monospace;width: fit-content;}.cm_error .err_content{margin:0.5rem 0;}.cm_error .show_msg{border-right:unset!important;border-left: 3px solid;}.cm_error .description{padding: 1rem;background-color: #f0f1f3;color: #464646;font-family: monospace;border-left: 3px solid #ccc;}.cm_error summary{color:#126eb7;padding:0.4rem 0;cursor:pointer}.cm_error .track{margin-top: 0.5rem;background: #fbfbfb;color: #607d8b;}.cm_error .track span{padding-right:5px}.cm_error a:not([href]),.cm_error a{color:#0a9adb;padding:0 4px}</style>";
        $box = " {$style}
                <div class='cm_error'>
                    <fieldset>
                        <legend>".$_title."</legend>
                        <div class='err_content'>".show_msg($_content,$_level)."</div>
                        <div>".cut_from_end(tracerout(false), "<p>")."</div>";
                        
                    "</fieldset>";
                if($_description != null OR $_track != null):
        $box .=     "<details><summary>more info</summary>";
                endif;
                if($_description != null):
                    
        $box .=   "<div class='description'>{$_description}</div>";
                endif;
                if($_track != null):
        $box .=     "<div class='track'>
                        <span>&there4;track</span><code>{$_track}</code>
                    </div>";
                endif;       
        $box .="</div>";
        if(is_devmode())
        {
            echo $box;
            
    
            
            die();
        }
        return $box;
    }
}