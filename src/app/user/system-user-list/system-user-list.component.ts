import { Component, OnInit } from '@angular/core';
import { slideToTop } from '../../router-animation/router-animation.component';
import { DatabaseService } from 'src/app/database.service';
import { ToastrManager } from 'ng6-toastr-notifications';

@Component({
  selector: 'app-system-user-list',
  templateUrl: './system-user-list.component.html',
  animations: [slideToTop()]
})
export class SystemUserListComponent implements OnInit {

  constructor(public db:DatabaseService,public toastr:ToastrManager) { }

  ngOnInit() {
    this.get_all_user(20,0);
  }

  pagelimit:any=20;
  start:any=0;
  pagenumber:any=0;
  totalpage:any=0;
  row_cn:any=0;
  user_list:any=[];
  loader:any="1";
  get_all_user(pagelimit:any=20,start:any=0)
  {
    this.search.type=1;
    this.pagelimit=parseInt(pagelimit);
    this.start=parseInt(start);
    this.db.getData({"pagelimit":this.pagelimit,"start":this.start,"search":this.search},"User/get_all_user")
    .subscribe(resp=>{
      console.log(resp);
      this.user_list=resp["data"];
      for(var i=0;i<this.user_list.length;i++)
      {
        this.user_list[i]["active"]=true;
        if(this.user_list[i]["status"] == 'Active')
        { this.user_list[i]["user_status"]=true; }
        else
        { this.user_list[i]["user_status"]=false; }
      }
      this.row_cn=resp["row"];
      this.totalpage=Math.ceil(this.row_cn/this.pagelimit);
      this.pagenumber=Math.ceil(this.start/this.pagelimit)+1;
      console.log(this.pagenumber);
      this.loader="";
    })
  }



  active:any = {};
  search:any={};
  type:any='';
  toggleterritory(key,action,index)
  {
    if(action == 'open')
    { 
      this.type="password";      
      this.user_list[index].active = true;
    }
    if(action == 'close')
    { 
      this.type="text";     
      this.user_list[index].active = false;
    }
  }

  update_status:any={};
  user_allowed(index,id)
  {
    this.update_status.id=id;
    console.log("yes");
    if(this.user_list[index].user_status)
    { this.update_status.status="Active"; }
    else
    { this.update_status.status="Deactive"}

    this.db.getData(this.update_status,"User/update_status")
      .subscribe(resp=>{
        console.log(resp);
        this.toastr.successToastr("User Activation Updated","Success!");
      });    
  }

  checkpage(){
    if(this.pagenumber=='' || this.pagenumber <= 0){
      this.pagenumber=1;
    }     
    if(parseInt(this.pagenumber) > parseInt(this.totalpage)){
      this.pagenumber=parseInt(this.totalpage);
    console.log(this.pagenumber);   
    console.log(this.totalpage);   
    }else{
      this.pagenumber=parseInt(this.pagenumber);
    }
  }

  refresh()
  {
    this.search={};
    this.get_all_user();
  }
}
