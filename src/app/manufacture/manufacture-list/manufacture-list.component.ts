import { Component, OnInit } from '@angular/core';
import { slideToTop } from '../../router-animation/router-animation.component';
import { DatabaseService } from 'src/app/database.service';
import { DialogService } from 'src/app/dialog.service';
import { Router } from '@angular/router';
import { ToastrManager } from 'ng6-toastr-notifications';
import { SessionService } from 'src/app/session.service';

@Component({
  selector: 'app-manufacture-list',
  templateUrl: './manufacture-list.component.html',
  animations: [slideToTop()]
})
export class ManufactureListComponent implements OnInit {

  constructor(public db:DatabaseService,public dialog:DialogService,public route:Router,public toastr:ToastrManager,public session:SessionService) { }
  session_data:any={};
  abq_user:any={};
  ngOnInit() {
    this.session_data=this.session.GetSession();
    this.abq_user=this.session_data.value;
    console.log(this.abq_user.login_type);
    this.get_all_manufecturer(100,0)
  }

  pagelimit:any=20;
  start:any=0;
  totalpage:any=0;
  pagenumber:any=0;
  result_list:any=[];
  row_cn:any=0;
  search:any={};
  loader:any="1";
  get_all_manufecturer(pagelimit:any=100,start:any=0)
  {
    this.pagelimit=parseInt(pagelimit);
    this.start=parseInt(start);
    this.db.getData({"pagelimit":this.pagelimit,"start":this.start,"search":this.search},"manufacturers/get")
    .subscribe(resp=>{
      console.log(resp);
      this.result_list=resp["data"];
      this.row_cn=resp["row_cn"];
      this.totalpage=Math.ceil(this.row_cn/this.pagelimit);
      this.pagenumber=Math.ceil(this.start/this.pagelimit)+1;
      console.log(this.result_list);
      this.loader="";
    })
  }

  refresh()
  {
    this.search={};
    this.get_all_manufecturer();
  }

  removedata(indx,id)
  {
    this.dialog.delete("Vendor")
    .then(resp=>{
      console.log(resp);
      if(resp)
      {
        this.db.getData(id,"manufacturers/delete")
        .subscribe(resp=>{
          console.log(resp);
          if(resp)
          {
            this.result_list.splice(indx,1);
            this.toastr.successToastr("Deleted","Success!");
          }
        })        
      }      
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

  
}
