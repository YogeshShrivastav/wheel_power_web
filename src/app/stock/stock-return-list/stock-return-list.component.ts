import { Component, OnInit, Renderer2 } from '@angular/core';
import { DatabaseService } from 'src/app/database.service';

@Component({
  selector: 'app-stock-return-list',
  templateUrl: './stock-return-list.component.html',
})
export class StockReturnListComponent implements OnInit {

  constructor(public db:DatabaseService,private renderer: Renderer2) { }

  ngOnInit() {
    this.get_product(20,0);
  }

  product_list:any=[];
  data:any=[];
  pagelimit:any=20;
  start:any=0;
  totalpage:any=0;
  pagenumber:any=0;
  product_cn:any=0;
  loader:any="1";
  type:any="return";
  total_return:any=0;
  total_qty:any=0;
  search:any={};
  get_product(pagelimit:any=20,start:any=0)
  {
    this.pagelimit=parseInt(pagelimit);
    this.start=parseInt(start);

    this.db.getData({"pagelimit":this.pagelimit,"start":this.start,"type":this.type,"search":this.search},"stock/get_stock_return")
    .subscribe(resp=>{
        console.log(resp);
        this.product_list=resp["data"];
        for(var i=0;i<this.product_list.length;i++)
        {
          for(var j=0;j<this.product_list[i].product.length; j++)
          {
            this.total_return=parseInt(this.product_list[i].product[j].warehouse_qty)+parseInt(this.product_list[i].product[j].shop_qty);
            this.product_list[i].product[j]["total_return"]=this.total_return;
            this.total_qty+=this.total_return;
          }
          this.product_list[i]["total_qty"]=this.total_qty;
          this.total_qty=0;
        }
        this.product_cn=resp["row_cn"];
        this.totalpage=Math.ceil(this.product_cn/this.pagelimit);
        this.pagenumber=Math.ceil(this.start/this.pagelimit)+1;
        console.log(this.product_list);
        this.loader="";
    });
  }

  refresh()
  {
    this.search={};
    this.get_product();
  }
  deleteproduct(id,indx)
  {    
    //console.log("indel");        
    this.db.getData(id,"products/delete")
    .subscribe(resp=>{
    console.log(resp);
    this.product_list.splice(indx,1);
    });   
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
