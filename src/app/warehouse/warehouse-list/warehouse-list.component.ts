import { Component, OnInit } from '@angular/core';
import { DatabaseService } from 'src/app/database.service';
import { ToastrManager } from 'ng6-toastr-notifications';
import { DialogService } from 'src/app/dialog.service';

@Component({
  selector: 'app-warehouse-list',
  templateUrl: './warehouse-list.component.html',
})
export class WarehouseListComponent implements OnInit {

  constructor(public db:DatabaseService,public toastr:ToastrManager,public dialog:DialogService) { }

  pagelimit:any=20;
  start:any=0;
  totalpage:any=0;
  pagenumber:any=0;
  row_cn:any=0;
  search:any={};
  loader:any="1";
  ngOnInit() {
    this.get_warehouse(20,0);
  }

  warehouse_list:any =[];
  get_warehouse(pagelimit:any=20,start:any=0)
  {

    this.pagelimit=parseInt(pagelimit);
    this.start=parseInt(start);
    this.db.getData({"pagelimit":this.pagelimit,"start":this.start,"search":this.search},"warehouse/warehouse_list")
    .subscribe(resp=>{
      console.log(resp);
      this.warehouse_list = resp['warehouse_list'];
      for(var i=0;i<this.warehouse_list.length;i++)
      {
        this.warehouse_list[i]["active"]=true;
      }
      this.row_cn = resp['total_rec'];
      this.totalpage=Math.ceil(this.row_cn/this.pagelimit);
      this.pagenumber=Math.ceil(this.start/this.pagelimit)+1;
      console.log(this.warehouse_list);
      if(resp["row_cn"] == 0){
        this.toastr.errorToastr("As per Entered Keywords..","No Record found!");
      }
      this.loader="";
    })
  }

  active:any = {};
  type:any='';
  toggleterritory(key,action,index)
  {
    if(action == 'open')
    { 
      this.type="password";      
      this.warehouse_list[index].active = true;
    }
    if(action == 'close')
    { 
      this.type="text";     
      this.warehouse_list[index].active = false;
    }
  }

  removedata(indx,id)
  {
    this.dialog.delete("Warehouse")
    .then(resp=>{
      console.log(resp);
      if(resp)
      {
        this.db.getData(id,"warehouse/delete")
        .subscribe(resp=>{
          console.log(resp);
          if(resp)
          {
            this.warehouse_list.splice(indx,1);
            this.toastr.successToastr("Deleted","Success!");
          }
        })        
      }      
    })
  }

  refresh()
  {
    this.search={};
    this.get_warehouse();
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
