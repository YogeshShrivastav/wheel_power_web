import { Component, OnInit } from '@angular/core';
import { DatabaseService } from 'src/app/database.service';

@Component({
  selector: 'app-customer-list',
  templateUrl: './customer-list.component.html',
})
export class CustomerListComponent implements OnInit {

  constructor(public db:DatabaseService) { }
  session_data:any={};
  abq_user:any={};
  product:any={};
  product_list:any=[];
  data:any=[];
  pagelimit:any=20;
  start:any=0;
  totalpage:any=0;
  pagenumber:any=0;
  customer_cn:any=0;
  loader:any="";
  type:any="add";
  search:any={};
  in_stock_row:any=0;
  out_of_stock_row:any=0;
  stock_alert_row:any=0;
  ngOnInit() {
    this.get_customer(20,0);
  }

  customer_list:any=[];
  get_customer(pagelimit:any=20,start:any=0)
  {
    this.pagelimit=parseInt(pagelimit);
    this.start=parseInt(start);
    this.db.getData({"pagelimit":this.pagelimit,"start":this.start,"search":this.search},"customer/get_all_customer")
    .subscribe(resp=>{
      console.log(resp);
      this.customer_list = resp['customer_list'];
      this.customer_cn = resp['total_rec'];
      this.totalpage=Math.ceil(this.customer_cn/this.pagelimit);
      this.pagenumber=Math.ceil(this.start/this.pagelimit)+1;
    })
  }
  refresh()
  {
    this.search = {};
    this.get_customer();
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
