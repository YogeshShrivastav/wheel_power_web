import { Component, OnInit, Renderer2} from '@angular/core';
import { slideToTop } from '../../router-animation/router-animation.component';
import { DatabaseService } from 'src/app/database.service';
import { ToastrManager } from 'ng6-toastr-notifications';
import { SessionService } from 'src/app/session.service';
import { MatDialog } from '@angular/material';
import { WarehouseDialogComponent } from 'src/app/warehouse-dialog/warehouse-dialog.component';
@Component({
  selector: 'app-stock-list',
  templateUrl: './stock-list.component.html', animations: [slideToTop()]
})
export class StockListComponent implements OnInit {

  constructor(public db:DatabaseService,private renderer: Renderer2,public toastr:ToastrManager,public session:SessionService,public dialog:MatDialog) { }
  session_data:any={};
  abq_user:any={};
  product:any={};
  ngOnInit() {    
    this.search.stock_type = 'instock';
    this.session_data=this.session.GetSession();
    this.abq_user=this.session_data.value;
    if(this.abq_user.login_type == 'admin')
    {
      this.get_warehouse();
    }
    else
    {
      this.warehouse_id = this.abq_user.id;
      console.log(this.warehouse_id);
    }
    
    this.get_warehouse_product(100,0);
    this.get_product_brand();
   }
   product_list:any=[];
   data:any=[];
   pagelimit:any=100;
   start:any=0;
   totalpage:any=0;
   pagenumber:any=0;
   product_cn:any=0;
   loader:any="1";
   type:any="add";
   search:any={};
   warehouse_id:any;
   in_stock_row:any=0;
   out_of_stock_row:any=0;
   stock_alert_row:any=0;
   warehouse:any=[];
   get_warehouse()
   {
     this.db.getData("","warehouse/stock_warehouse_list")
     .subscribe(resp=>{
       console.log(resp);
       this.warehouse = resp['warehouse_list'];
     })
   }

  get_warehouse_product(pagelimit:any=100,start:any=0)
  {
    this.pagelimit=parseInt(pagelimit);
    this.start=parseInt(start);
    this.db.getData({"pagelimit":this.pagelimit,"start":this.start,"search":this.search,'warehouse_id':this.warehouse_id},"warehouse/warehouse_product_list")
    .subscribe(resp=>{
        console.log(resp);
        this.product_list=resp["data"];
        this.in_stock_row=resp["in_st_cn"];
        this.stock_alert_row=resp["st_al_cn"];
        this.out_of_stock_row=resp["out_st_cn"];
        
        if(this.search.stock_type == 'instock')
        {
          this.totalpage=Math.ceil(this.in_stock_row/this.pagelimit);
          this.pagenumber=Math.ceil(this.start/this.pagelimit)+1;
        }

        if(this.search.stock_type == 'stockalert')
        {
          this.totalpage=Math.ceil(this.stock_alert_row/this.pagelimit);
          this.pagenumber=Math.ceil(this.start/this.pagelimit)+1;
        }

        if(this.search.stock_type == 'outofstock')
        {
          this.totalpage=Math.ceil(this.out_of_stock_row/this.pagelimit);
          this.pagenumber=Math.ceil(this.start/this.pagelimit)+1;
        }
       
        console.log(this.product_list);
        this.loader="";
    });
  }
 
   refresh()
   {
      this.search.brand='';
      this.search.category='';
      this.search.product_name='';
      this.search.cat_no='';
      if(this.abq_user.login_type != 'admin')
      { 
        this.instock = 1;
        this.stockalert=0; 
        this.outofstock=0;
        this.search.stock_type = 'instock';
      }
      this.get_warehouse_product(100,0);
   }


   deleteproduct(id,indx)
   {    
     this.db.getData(id,"products/delete")
     .subscribe(resp=>{
     console.log(resp);
     this.product_list.splice(indx,1);
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

  brand_list:any=[];
  get_product_brand()
  {
    this.db.getData("","products/get_product_brand")
    .subscribe(resp=>{
        console.log(resp);
        this.brand_list=resp
        this.fltr_brand=this.brand_list;
    });
  }

  category_list:any=[];
  get_product_category(category)
  {
    console.log(category);
    this.db.getData(category,"products/get_product_category")
    .subscribe(resp=>{
        console.log(resp);
        if(resp['val_type'] == 'category')
        {
          this.category_list=resp['value'];
          this.fltr_category=this.category_list;
        }
        else
        {

        }
    });
  }


  fltr_category:any=[];
  category_array_filter(data,array,index_val)
  {
    this.fltr_category=this.filter(data.toUpperCase(),array,index_val);
  }

  fltr_sub_category:any=[];
  subcategory_array_filter(data,array,index_val)
  {
    this.fltr_sub_category=this.filter(data.toUpperCase(),array,index_val);
  }

  fltr_brand:any=[];
  brand_array_filter(data,array,index_val)
  {
    this.fltr_brand=this.filter(data.toUpperCase(),array,index_val);
  }

  new_array:any=[];
  filter(search_word,search_array,index_val)
  {
    this.new_array=[];
    for(var i=0; i<search_array.length; i++)
    {
      if(search_array[i][index_val].toUpperCase().search(search_word) !==-1)
      {
        this.new_array.push(search_array[i]);
      }
    }
    return this.new_array;
  }
    
  instock:any=1;
  stockalert:any='';
  outofstock:any='';
  classactive(value)
  {
    this.loader = 1;
    if(value == 'in')
    { this.instock = 1;
      this.stockalert=0; 
      this.outofstock=0;
      this.search.stock_type = 'instock';
    }
    else if(value == 'alert')
    { 
      this.stockalert=1; 
      this.instock = 0;
      this.outofstock=0; 
      this.search.stock_type = 'stockalert';
    }
    else if(value == 'out')
    { 
      this.outofstock = 1;   
      this.instock = 0; 
      this.stockalert=0; 
      this.search.stock_type = 'outofstock';
    }
  }

  open_wharehouse_dialog(p_id,total_stock)
  {
    const dialogRef  = this.dialog.open(WarehouseDialogComponent,{
      data:{
        product_id:p_id,
        total_stock:total_stock
      },
    });

    dialogRef.afterClosed().subscribe(result=>{
      console.log(result);
    });
  }

}
