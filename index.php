<?php
require_once 'init.php';
$theme  = $ini['general']['theme'];
$class  = isset($_REQUEST['class']) ? $_REQUEST['class'] : '';
$public = in_array($class, $ini['permission']['public_classes']);

// AdiantiCoreApplication::setRouter(array('AdiantiRouteTranslator', 'translate'));

new TSession;
ApplicationTranslator::setLanguage( TSession::getValue('user_language'), true );
BuilderTranslator::setLanguage( TSession::getValue('user_language'), true );

$content = BuilderTemplateParser::init('layout');
$content = ApplicationTranslator::translateTemplate($content);

echo $content;

if (TSession::getValue('logged') OR $public)
{
    if ($class)
    {
        $method = isset($_REQUEST['method']) ? $_REQUEST['method'] : NULL;
        if(TSession::getValue('userid')){
            TTransaction::open('minicrm');
            $preferenciaSistema = (PreferenciaSistema::where('system_users_id','=',TSession::getValue('userid'))->first());

            if(!$preferenciaSistema){
                $preferenciaSistema = new PreferenciaSistema();
                $preferenciaSistema->system_users_id = TSession::getValue('userid');
                $preferenciaSistema->zoom = 100;
                $preferenciaSistema->menu_fixado = 0;
                $preferenciaSistema->store();
            }
            
            TTransaction::close();
            TScript::create('$("body").css("zoom","'.$preferenciaSistema->zoom.'%");');
            
            if(isset($preferenciaSistema->menu_fixado) && $preferenciaSistema->menu_fixado == 1)
            {
                 echo "
                <script>
                    var element = document.querySelector('.sidebar-mini');
                
                    element.classList.add('fixed');
                </script>
                ";
            }
            else 
            {
                 echo "
                <script>
                    var element = document.querySelector('.sidebar-mini');
                
                    element.classList.remove('fixed');
                </script>
                ";
            }
        }
        
        AdiantiCoreApplication::loadPage($class, $method, $_REQUEST);
    }
}
else
{
    if (isset($ini['general']['public_view']) && $ini['general']['public_view'] == '1')
    {
        if (!empty($ini['general']['public_entry']))
        {
            AdiantiCoreApplication::loadPage($ini['general']['public_entry'], '', $_REQUEST);
        }
    }
    else
    {
        AdiantiCoreApplication::loadPage('LoginForm', '', $_REQUEST);
    }
}
