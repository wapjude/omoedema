<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of superuser
 *
 * @author JUDE
 */
class superuser extends Base {
    //put your code here
    public function __construct() {
        parent::__construct();
        model("superuser_model", "superuser");
        model("decor_model","decor");
        $this->data["is_admin"] = true;
        $this->data["is_logged_in"] = Auth::admin();
        
        $this->template->Set_Skeleton("admin-template-skeleton");
        
        if($this->data["is_logged_in"]){
            $this->data["user"] = $this->superuser->get_admin();
            $this->data["count"]["users"] = $this->superuser->count_table("users");
            $this->data["count"]["categories"] = $this->superuser->count_table("categories");
            $this->data["site"]["categories"] = $this->decor->get_categories();
            $this->data["site"]["photos"] = $this->superuser->get_albums();
            $this->data["site"]["videos"] = $this->superuser->get_videos();
            $this->data["site"]["sliders"] = $this->superuser->get_sliders();
            $this->data["site"]["sub_plans"] = $this->superuser->get_plans();
            $this->data["site"]["misc"] = $this->superuser->get_misc();
            $this->data["site"]["admins"] = $this->superuser->get_admins();
        }
    }
    
    public function index(){
        $api = new Instamojo\Instamojo("e7a7d1a4469f363f72ab97d6cba80407", "7f2cf4478ec04014fe9c78f4f8f5ed63");
        
        if(!$this->data["is_logged_in"]){
            redirect(App::route("superuser", "login"));
        }
        $this->data["page"]["name"] = "Dashboard";
        $this->template->inject_view("admin-index",  $this->data);
    }
    
    public function login(){
         if(post("username") != null){
            $this->data["form"]["message"] =  $this->superuser->login();
        }
        Theme::Section("admin-login",  $this->data);
     
    }
    
    public function categories(){
        if(!$this->data["is_logged_in"]){
            redirect(App::route("superuser", "login"));
        }
        $this->data["page"]["name"] = "Categories";
        $this->template->inject_view("admin-category",  $this->data);
    }
    public function photos(){
        if(!$this->data["is_logged_in"]){
            redirect(App::route("superuser", "login"));
        }
         $this->data["page"]["name"] = "Manage Photos";
        $this->template->inject_view("admin-photos",  $this->data);
    }
    
    
    public function photo_album($id){
        if(!$this->data["is_logged_in"]){
            redirect(App::route("superuser", "login"));
        }
        $this->data["album"] = $this->superuser->get_albums($id);
        $this->data["page"]["name"] = $this->data["album"]["name"];
        $this->template->inject_view("photo",  $this->data);
        
    }
      public function videos(){
          if(!$this->data["is_logged_in"]){
            redirect(App::route("superuser", "login"));
        }
         $this->data["page"]["name"] = "Manage Videos";
        $this->template->inject_view("admin-videos",  $this->data);
    }
    
    public function settings(){
        if(!$this->data["is_logged_in"]){
            redirect(App::route("superuser", "login"));
        }
            $this->data["page"]["name"] = "Manage Admin";
        $this->template->inject_view("admin-settings",  $this->data);
        
    }
      public function sliders(){
          if(!$this->data["is_logged_in"]){
            redirect(App::route("superuser", "login"));
        }
         $this->data["page"]["name"] = "Manage Sliders";
        $this->template->inject_view("admin-sliders",  $this->data);
    }
    public function plans(){
        if(!$this->data["is_logged_in"]){
            redirect(App::route("superuser", "login"));
        }
         $this->data["page"]["name"] = "Manage Users";
        $this->template->inject_view("admin-plans",  $this->data);
    }
     public function users(){
         if(!$this->data["is_logged_in"]){
            redirect(App::route("superuser", "login"));
        }
         $this->data["page"]["name"] = "Manage Users";
         $this->data["site"]["users"] = $this->superuser->get_users();
        $this->template->inject_view("admin-users",  $this->data);
    }
    
    public function design(){
        if(!$this->data["is_logged_in"]){
            redirect(App::route("superuser", "login"));
        }
        $this->data["page"]["name"] = "Manage Page Content";
        $this->template->inject_view("admin-design",  $this->data);
    }


    public function requests($request){
        if(!Auth::admin()){
            redirect(base_url());
        }
        switch ($request){
            case "addcategory":
                $this->superuser->add_category();
                redirect(App::route("superuser","categories"));
                break;
            case "deletecategory":
                $this->superuser->delete_record("categories",  get("action"));
                 redirect(App::route("superuser","categories"));
                break;
            case "upload_photo":
                $data["image"] = App::Assets()->getUploads()->getImage($this->decor->upload("album_photo"));
                print json_encode($data);
                break;
            case "upload_video":
                $data["video"] = App::Assets()->getUploads()->getImage($this->decor->upload("file",".mp4"));
                print json_encode($data);
                break;
            
            case "addalbum":
                $this->superuser->add_photo_album();
                redirect(App::route("superuser","photos"));
                break;
            case "delete_album":
                $this->superuser->delete_photo_album(get("action"));
                redirect(App::route("superuser","photos"));
                break;
            case "addvideo":
                $this->superuser->addvideo();
                redirect(App::route("superuser","videos"));
                break;
            case "addsliders":
            $this->superuser->addsliders();
                redirect(App::route("superuser","sliders"));
                break;
            case "addsubplans":
                $this->superuser->add_plans();
                redirect(App::route("superuser", "plans"));
                break;
            case "deletesub":
                $this->superuser->delete_record("sub_plans",  get("action"));
                 redirect(App::route("superuser","plans"));
                break;
            case "delete_video":
                $this->superuser->delete_record("videos",  get("action"));
                 redirect(App::route("superuser","videos"));
                break;
            case "delete_user":
                  $this->superuser->delete_record("users",  get("action"));
                 redirect(App::route("superuser","videos"));
                break;
            case "delete_sliders":
                $this->superuser->delete_record("sliders",  get("action"));
                 redirect(App::route("superuser","sliders"));
                break;
            case "updateviews":
                $this->superuser->update_misc();
                 redirect(App::route("superuser","design"));
                break;
            case "addadmin":
                $this->superuser->create_admin();
                redirect(App::route("superuser", "settings"));
                break;
            case "deleteadmin":
                $this->superuser->delete_record("admin",  get("action"));
                 redirect(App::route("superuser","settings"));
                
            default :
                show_404();
                break;

                }
    }
    
}
