import { Component, OnInit } from '@angular/core';
import { DatabaseService } from 'src/app/database.service';
import { MatDialog } from '@angular/material';
import { OrderDispatchDetailComponent } from '../order-dispatch-detail/order-dispatch-detail.component';
import { OrderPaymentDialogComponent } from '../order-payment-dialog/order-payment-dialog.component';

@Component({
  selector: 'app-order-payment',
  templateUrl: './order-payment.component.html',
})
export class OrderPaymentComponent implements OnInit {

  constructor(public db:DatabaseService,public dialog:MatDialog) { }
  search:any={};
  pagelimit:any=100;
  start:any=0;
  totalpage:any=0;
  pagenumber:any=0;
  payment_cn:any=0;
  loader:any="1";
  show:boolean=false;
  toggle:boolean=false;
  ngOnInit() {
    this.get_payment(100,0);
  }

  payment_list:any=[];
  get_payment(pagelimit:any=100,start:any=0)
  {
    this.pagelimit=parseInt(pagelimit);
    this.start=parseInt(start);
    
    this.db.getData({"pagelimit":this.pagelimit,"start":this.start},"order/order_payment")
    .subscribe(resp=>{
      console.log(resp);
      this.payment_list = resp['payment_list'];
      this.payment_cn = resp['payment_cn'];
      this.totalpage=Math.ceil(this.payment_cn/this.pagelimit);
      this.pagenumber=Math.ceil(this.start/this.pagelimit)+1;
      this.loader="";
    })
  }

  order_payment:any=[];
  order_credit_detail:any={};
  open_varify_dialog(indx)
  {
    console.log((this.payment_list[indx]['payment_modes']));
    this.order_credit_detail = this.payment_list[indx]['credit_payment'];
    this.order_payment = this.payment_list[indx]['payment_modes'];
  }

  varify:any={};
  varify_payment(id,action)
  {
    this.loader="1";
    console.log(id);
    this.varify.id=id;
    this.varify.action=action;
    this.db.getData(this.varify,"order/varify_payment")
    .subscribe(resp=>{
      console.log(resp);
      if(resp == 'success')
      {
        this.get_payment();
        this.toggle = false;
      }
    });
  }

  active:boolean=false;
   update:any={};
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
