<?php
/*
Plugin Name: 设置用户头衔
Plugin URI: https://www.xn--4qsv20l.com/
Description: 设置用户头衔
Version: 1.0
Author: 钟声
Author URI: https://www.xn--4qsv20l.com/
License: GPLv2
*/

class zs_renzheng{
    function zs_renzheng(){
        // 创建菜单
        add_action( 'admin_menu', array( $this, 'create_menu' ) );
        
        // 输出作者信息的钩子
        add_filter('the_author',array( $this, 'print_touxian' ));
        add_filter('get_comment_author',array( $this, 'print_touxian_pl' ));

        //使用ajax保存信息
        wp_enqueue_script( 'hc_test', plugins_url('js/hc_test.js', __FILE__), array('jquery') );
        wp_localize_script( 'hc_test', 'ajax_object', array( 'ajax_url' => admin_url( 'admin-ajax.php' ) ) );
        
        add_action( 'wp_ajax_zs_touxian' , array( $this, 'touxian_update') );
        add_action( 'wp_ajax_nopriv_zs_touxian' , array( $this, 'touxian_update') );
        // 重新获取已有头衔
        add_action( 'wp_ajax_zs_reset' , array( $this, 'zs_reset') );
        add_action( 'wp_ajax_nopriv_zs_reset' , array( $this, 'zs_reset') );

    }

    // 更新头衔信息_ajax
    function touxian_update(){
        $user_title_arr = array("zs_color"=>$_POST['zs_color'],"zs_ico"=>$_POST['zs_ico'],"zs_weizhi"=>$_POST['radio'],"zs_title"=>$_POST['zs_title']);
        update_option('touxian_'.$_POST['zs_username'],$user_title_arr);


        // 保存到已有头衔数组
        $touxian_all_arr = array();
        $touxian_all = array($_POST['zs_username']);
        $touxian_all = array_merge(get_option('touxian_all',$touxian_all_arr),$touxian_all);
        $touxian_all = array_unique($touxian_all);
        update_option('touxian_all',$touxian_all);

        echo 'ok';
        wp_die();
    }    

    // 创建设置下的子菜单
    function create_menu() {

        add_submenu_page( 
            'options-general.php',
            '头衔设置首页', 
            '头衔', 
            'manage_options', 
            'touxian',
            array( $this, 'settings_page' )
            // plugins_url( '/images/icon.png', __FILE__ )
        );
    }

    // 输出插件界面html
    function settings_page() {
        // <div id="message" class="updated">设置保存成功</div>
        // <div id="message" class="error">保存出现错误</div>

        echo '
        <div class="wrap">
            <h2>头衔设置</h2>
            <hr>
                <table class="form-table">
                    <tr valign="top">
                        <th><label for="xingming">用户名：</label></th>
                        <td><input id="zs_username" name="zs_username" value="钟声" /></td>
                    </tr>
                    <tr valign="top">
                        <th><label for="xingming">头衔：</label></th>
                        <td><input id="zs_title" name="zs_title" value="官方编辑"/>
                        &nbsp;&nbsp;设置no为取消头衔。
                        </td>
                    </tr>
                    <tr valign="top">
                        <th><label for="xingming">图标：</label></th>
                        <td><input id="zs_ico" name="zs_ico" value="class=&quot;fa fa-vimeo-square&quot; aria-hidden=&quot;true&quot;"/>
                            &nbsp;&nbsp;注意：不填写i标签><a target="_blank" href="http://fontawesome.io/">更多</a>

                        </td>

                    </tr>
                    <tr valign="top">
                        <th><label for="xingming">图标颜色：</label></th>
                        <td><input id="zs_color" name="zs_color" value="#16C0F8"/></td>
                    </tr>

        
                    <tr valign="top">
                        <th><label for="xingbie">位置</label></th>
                        <td>
                            <input type="radio" name="zs_weizhi" value="qb" /> 全部位置
                            <input type="radio" name="zs_weizhi" value="pl" checked/> 仅评论区
                        </td>
                    </tr>
                    <font id="error_color"></font>

                    <tr valign="top">
                        <td>
                            <input type="submit" id="zs_save" name="save" value="保存设置" class="button-primary" />
                        </td>
                    </tr>



                    <table class="widefat striped">
                    <hr>
                    <h2>已有头衔：</h2>
                        <thead>
                            <tr>
                                <th>用户名</th>
                                <th>头衔</th>
                                <th>图标</th>
                                <th>图标颜色</th>
                                <th>位置</th>
                            </tr>
                        </thead>
                        <tbody>';

                       $touxian_all_arr = get_option('touxian_all');
                        if ($touxian_all_arr) {
                            foreach ($touxian_all_arr as $key => $value) {
                                $touxian_arr = get_option('touxian_'.$value);
                                if ($touxian_arr['zs_title'] != 'no') {
                                    $weizhi = ($touxian_arr['zs_weizhi']=='pl') ? "仅评论区" : "全部位置" ;
                                    echo '
                                      <tr>
                                            <td>'.$value.'</td>
                                            <td>'.$touxian_arr['zs_title'].'</td>
                                            <td>'.stripslashes($touxian_arr['zs_ico']).'</td>
                                            <td>'.$touxian_arr['zs_color'].'</td>
                                            <td>'.$weizhi.'</td>
                                        </tr>
                                    ';
                                }
                            }
                        }
    
                            echo '
                        </tbody>
                    </table>

                </table>
                
        </div>

        ';
    }

    //输出头衔信息
    function print_touxian($author){
        $touxian_arr = get_option('touxian_'.$author,false);
        if ($touxian_arr['zs_title'] != 'no') {
            if ($touxian_arr['zs_weizhi'] == 'qb') {
                $author = $author . '&nbsp;<i style="color:'.$touxian_arr['zs_color'].'" title="'.$touxian_arr['zs_title'].'" alt="'.$touxian_arr['zs_title'].'" '.stripslashes($touxian_arr['zs_ico']).'></i>';       
            }  
        }
        return $author;
    }

    //输出头衔信息_评论
    function print_touxian_pl($author){
        $touxian_arr = get_option('touxian_'.$author,false);
        if ($touxian_arr['zs_title'] != 'no') {
            $author = $author . '&nbsp;<i style="color:'.$touxian_arr['zs_color'].'" title="'.$touxian_arr['zs_title'].'" alt="'.$touxian_arr['zs_title'].'" '.stripslashes($touxian_arr['zs_ico']).'></i>';       
        }
        return $author;
    }

    // 重新获取已有头衔
    function zs_reset(){

       $touxian_all_arr = get_option('touxian_all');
        if ($touxian_all_arr) {
            foreach ($touxian_all_arr as $key => $value) {
                $touxian_arr = get_option('touxian_'.$value);
                if ($touxian_arr['zs_title'] != 'no') {
                    $weizhi = ($touxian_arr['zs_weizhi']=='pl') ? "仅评论区" : "全部位置" ;
                    $html.= '
                      <tr>
                            <td>'.$value.'</td>
                            <td>'.$touxian_arr['zs_title'].'</td>
                            <td>'.stripslashes($touxian_arr['zs_ico']).'</td>
                            <td>'.$touxian_arr['zs_color'].'</td>
                            <td>'.$weizhi.'</td>
                        </tr>
                    ';
                }
            }
            echo $html;
        }


    }

}
new zs_renzheng();