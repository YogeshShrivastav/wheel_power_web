import { Component, OnInit } from '@angular/core';
import { DatabaseService } from 'src/app/database.service';
import { Router, ActivatedRoute } from '@angular/router';
import { SessionService } from 'src/app/session.service';

@Component({
  selector: 'app-order-dispatch',
  templateUrl: './order-dispatch.component.html',
})
export class OrderDispatchComponent implements OnInit {

  session_data:any={};
  abq_user:any={};
  constructor(public db:DatabaseService,public route:ActivatedRoute,public session:SessionService) {
    this.session_data=this.session.GetSession();
    this.abq_user=this.session_data.value;
    console.log(this.abq_user.id);

    this.route.params.subscribe(resp=>{
      console.log(resp);
      this.page_type = resp.type;
      console.log(this.page_type);
      if(this.page_type == 'packing_pend')
      {
        this.packing_pend = true;
        this.dispatch_pend = false;
        this.dispatch = false;
      }
      else if(this.page_type == 'dispatch_pend')
      {
        this.packing_pend = false;
        this.dispatch_pend = true;
        this.dispatch = false;
      }
      else if(this.page_type == 'dispatch')
      {
        this.packing_pend = false;
        this.dispatch_pend = false;
        this.dispatch = true;
      }
    });
   }

  pending:any=true;
  ngOnInit() {
    this.get_assign_order(100,0);
  }


  refresh()
  {
    this.search={};
    this.get_assign_order();
  }

  page_type:any='';
  packing_pend:any=false
  dispatch_pend:any=false
  dispatch:any=false
  classactive(data)
  {
    this.search={};
    console.log(data);
    if(data == 'packing_pend')
    {
      this.packing_pend = true;
      this.dispatch_pend = false;
      this.dispatch = false;
      this.page_type = 'packing_pend'
    }

    if(data == 'dispatch_pend')
    {
      this.packing_pend = false;
      this.dispatch_pend = true;
      this.dispatch = false;
      this.page_type = 'dispatch_pend'
    }

    if(data == 'dispatch')
    {
      this.packing_pend = false;
      this.dispatch_pend = false;
      this.dispatch = true;
      this.page_type = 'dispatch'
    }
  }

  search:any={};
  pagelimit:any=100;
  start:any=0;
  totalpage:any=0;
  pagenumber:any=0;
  pack_pend_cn:any=0;
  disp_pend_cn:any=0;
  dispatch_cn:any=0;
  loader:any="";
  order_list:any=[];
  get_assign_order(pagelimit:any=100,start:any=0)
  {
    this.loader='1';
    this.db.getData({"pagelimit":this.pagelimit,"start":this.start,"page_type":this.page_type,'warehouse_id':this.abq_user.id,'search':this.search},"order/get_assign_order")
    .subscribe(resp=>{
      console.log(resp);
      this.loader='';
      this.order_list = resp['order_list'];
      this.pack_pend_cn = resp['pack_pend_cn'];
      this.disp_pend_cn = resp['disp_pend_cn'];
      this.dispatch_cn = resp['dispatch_cn'];
    })
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
}
