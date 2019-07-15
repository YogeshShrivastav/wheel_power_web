import { Component, OnInit } from '@angular/core';
import { DatabaseService } from 'src/app/database.service';
import { DialogService } from 'src/app/dialog.service';
import { ToastrManager } from 'ng6-toastr-notifications';
import { ActivatedRoute } from '@angular/router';

@Component({
  selector: 'app-order-list',
  templateUrl: './order-list.component.html',
})
export class OrderListComponent implements OnInit {

  search:any={};
  pagelimit:any=100;
  start:any=0;
  totalpage:any=0;
  pagenumber:any=0;
  loader:any="";
  show:boolean=false;
  constructor(public db:DatabaseService,public dialog:DialogService,public toastr:ToastrManager,public route:ActivatedRoute) {
    this.route.params.subscribe(resp=>{
      console.log(resp);
      this.page_type = resp.type;
      console.log(this.page_type);

      if(this.page_type == 'pending')
      {
        this.pending=true;this.assign_to_warehouse=false;this.invoice=false;
        this.packing=false;this.dispatch=false;
      }
      if(this.page_type == 'assign_to_warehouse')
      {
        this.pending=false;this.assign_to_warehouse=true;this.invoice=false;
        this.packing=false;this.dispatch=false;
      }

      if(this.page_type == 'packing')
      {
        this.pending=false;this.assign_to_warehouse=false;this.invoice=false;
        this.packing=true;this.dispatch=false;
      }

      if(this.page_type == 'invoice')
      {
        this.pending=false;this.assign_to_warehouse=false;this.invoice=true;
        this.packing=false;this.dispatch=false;
      }

      if(this.page_type == 'dispatch')
      {
        this.pending=false;this.assign_to_warehouse=false;this.invoice=false;
        this.packing=false;this.dispatch=true;
      }

      })
    }

  ngOnInit() {
    this.get_orders(100,0);
  }

  order_list:any=[];
  pending_cn:any=0;
  assign_cn:any=0;
  packing_cn:any=0;
  invoice_cn:any=0;
  dispatch_cn:any=0;
  get_orders(pagelimit:any=100,start:any=0)
  {
    this.pagelimit=parseInt(pagelimit);
    this.start=parseInt(start);
    this.loader="1";
    this.db.getData({"pagelimit":this.pagelimit,"start":this.start,"page_type":this.page_type,'search':this.search},"order/order_list")
    .subscribe(resp=>{
      console.log(resp);
      this.order_list = resp['order_list'];
      this.pending_cn = resp['pending_count'];
      this.assign_cn = resp['assign_count'];
      this.packing_cn = resp['packing_count'];
      this.invoice_cn = resp['invoice_count'];
      this.dispatch_cn = resp['dispatch_count'];
      if(this.page_type == 'pending')
      {
        this.totalpage=Math.ceil(this.pending_cn/this.pagelimit);
        this.pagenumber=Math.ceil(this.start/this.pagelimit)+1;
      }

      if(this.page_type == 'assign_to_warehouse')
      {
        this.totalpage=Math.ceil(this.assign_cn/this.pagelimit);
        this.pagenumber=Math.ceil(this.start/this.pagelimit)+1;
      }

      if(this.page_type == 'packing')
      {
        this.totalpage=Math.ceil(this.packing_cn/this.pagelimit);
        this.pagenumber=Math.ceil(this.start/this.pagelimit)+1;
      }

      if(this.page_type == 'invoice')
      {
        this.totalpage=Math.ceil(this.invoice_cn/this.pagelimit);
        this.pagenumber=Math.ceil(this.start/this.pagelimit)+1;
      }

      if(this.page_type == 'dispatch')
      {
        this.totalpage=Math.ceil(this.dispatch_cn/this.pagelimit);
        this.pagenumber=Math.ceil(this.start/this.pagelimit)+1;
      }
      this.loader="";
    })
  }


  refresh()
  {
    this.search={};
    this.get_orders();
  }

  checkpage()
  {
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

  deleteproduct(ord_id,indx)
  {    
    this.dialog.delete("Order")
    .then(resp=>{
      if(resp)
      {
        this.db.getData(ord_id,"order/delete_order")
        .subscribe(resp=>{
          console.log(resp);
          this.order_list.splice(indx,1);
          this.toastr.successToastr("Deleted","Success!");
        })
      }
    });       
  }

  pending:any=false;assign_to_warehouse:any=false;invoice:any=false;
  packing:any=false;dispatch:any=false;
  page_type:any='';
  classactive(args)
  {
    this.search={};
    if(args == 'pending')
    {
      this.page_type = 'pending';
      this.pending=true;this.assign_to_warehouse=false;this.invoice=false;
      this.packing=false;this.dispatch=false;
    }
    if(args == 'assign_to_warehouse')
    {
      this.page_type = 'assign_to_warehouse';
      this.pending=false;this.assign_to_warehouse=true;this.invoice=false;
      this.packing=false;this.dispatch=false;
    }

    if(args == 'packing')
    {
      this.page_type = 'packing';
      this.pending=false;this.assign_to_warehouse=false;this.invoice=false;
      this.packing=true;this.dispatch=false;
    }

    if(args == 'invoice')
    {
      this.page_type = 'invoice';
      this.pending=false;this.assign_to_warehouse=false;this.invoice=true;
      this.packing=false;this.dispatch=false;
    }

    if(args == 'dispatch')
    {
      this.page_type = 'dispatch';
      this.pending=false;this.assign_to_warehouse=false;this.invoice=false;
      this.packing=false;this.dispatch=true;
    }
  }
  view_full(enc_id)
  {
    window.open('/order-detail/'+enc_id+'/packing/full');
  }
}
