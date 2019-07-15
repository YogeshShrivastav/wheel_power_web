import { Component, OnInit } from '@angular/core';
import { slideToTop } from '../../../router-animation/router-animation.component';
import { DatabaseService } from 'src/app/database.service';
import { SessionService } from 'src/app/session.service';
import { Router } from '@angular/router';

@Component({
  selector: 'app-stock-shift-list',
  templateUrl: './stock-shift-list.component.html',
  animations: [slideToTop()]
})
export class StockShiftListComponent implements OnInit {

  session_data:any=[];
  abq_user:any={}
  constructor(public db:DatabaseService,public session:SessionService,public route:Router) {
    this.session_data=this.session.GetSession();
    this.abq_user=this.session_data.value;
    console.log(this.abq_user);
    
   }

  loader:any="";
  ngOnInit() {
    this.search.transfer_type = 'pending';
    this.get_stock_shift(100,0);
  }

  pagelimit:any=100;
  totalpage:any=0;
  pagenumber:any=0;
  start:any=0;
  shifting_list:any=[];
  row_count:any=0;
  sum:any=0;
  search:any={};
  get_stock_shift(pagelimit:any=100,start:any=0)
  {
    this.loader="1";
    this.pagelimit=parseInt(pagelimit);
    this.start=parseInt(start);
    this.db.getData({"pagelimit":this.pagelimit,"start":this.start,"search":this.search,'warehouse_id':this.abq_user.id},"stock/stock_transfer_list")
    .subscribe(resp=>{
      console.log(resp);
      this.shifting_list=resp["data"];
      for(var i=0;i<this.shifting_list.length; i++)
      {
        if(this.shifting_list[i]["status"] == 'pending')
        {
          this.shifting_list[i]["transfer_status"]=false;
        }

        if(this.shifting_list[i]["status"] == 'received')
        {
          this.shifting_list[i]["transfer_status"]=true;
        }
      }
      this.row_count=resp["row"];
      this.totalpage=Math.ceil(this.row_count/this.pagelimit);
      this.pagenumber=Math.ceil(this.start/this.pagelimit)+1;
      this.loader="";
    });

    console.log(this.shifting_list);
    
  }

  refresh()
  {
    this.search={};
    this.get_stock_shift();
  }

  stock_status(id)
  {
    console.log(id);
    this.route.navigate(['/stock-sifting-detail/'+id])
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
  pending:any=1;
  received:any='';
  classactive(value)
  {
    this.loader = 1;
    if(value == 'pending')
    { 
      this.pending = 1;
      this.received=0; 
      this.search.transfer_type = 'pending';
    }
    else if(value == 'received')
    { 
      this.received = 1;
      this.pending=0; 
      this.search.transfer_type = 'received';
    }
  }
}
