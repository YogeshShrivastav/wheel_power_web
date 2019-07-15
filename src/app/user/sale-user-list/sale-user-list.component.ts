import { Component, OnInit } from '@angular/core';
import { slideToTop } from '../../router-animation/router-animation.component';
import { DatabaseService } from 'src/app/database.service';
import { ToastrManager } from 'ng6-toastr-notifications';
import { DialogService } from 'src/app/dialog.service';

@Component({
  selector: 'app-sale-user-list',
  templateUrl: './sale-user-list.component.html',
  animations: [slideToTop()]
  
})
export class SaleUserListComponent implements OnInit {
  
  constructor(public db:DatabaseService,public toastr:ToastrManager,public dialog:DialogService) { }
  
  search:any={};
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
    this.search.type=3;
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
      // if(this.row_cn == 0){
      //   this.toastr.errorToastr("As per Entered Keywords..","No Record found!");
      // }
      this.loader="";
    })
  }
  
  
  checkpage(){
    if((this.pagenumber == '')||(this.pagenumber <= 0)){
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
  
  deleteproduct(cus_id,indx)
  {    
    this.dialog.delete("User")
    .then(resp=>{
      if(resp)
      {
        this.db.getData(cus_id,"user/delete_user")
        .subscribe(resp=>{
          console.log(resp);
          this.user_list.splice(indx,1);
          this.toastr.successToastr("Deleted","Success!");
        })
      }
    });       
  }
  
  active:any = {};
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
  refresh()
  {
    this.search={};
    this.get_all_user();
  }
}
