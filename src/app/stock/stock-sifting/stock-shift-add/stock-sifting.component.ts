import { Component, OnInit, ViewChild, AfterViewInit, Renderer } from '@angular/core';
import { slideToTop } from '../../../router-animation/router-animation.component';
import { DatabaseService } from 'src/app/database.service';
import { SessionService } from 'src/app/session.service';
import { DialogService } from 'src/app/dialog.service';
import { Router } from '@angular/router';
import {ElementRef,Renderer2} from '@angular/core';
import { DomSanitizer } from '@angular/platform-browser';
import { ToastrManager } from 'ng6-toastr-notifications';


@Component({
  selector: 'app-stock-sifting',
  templateUrl: './stock-sifting.component.html',
  animations: [slideToTop()]
})
export class StockSiftingComponent implements OnInit {
  constructor(public db:DatabaseService,public session:SessionService,public dialog:DialogService,public route:Router,private rd: Renderer,private sanitizer:DomSanitizer,public toastr:ToastrManager) { }

  @ViewChild('slipval') obj:ElementRef;

  
  session_data:any=[];
  abq_user:any=[];
  slip_id:any=0;
  productdata:any=0;
  loader:any="";
  data:any={};
  ngOnInit() {
    this.session_data=this.session.GetSession();
    this.abq_user=this.session_data.value;
    console.log(this.abq_user);
    this.productdata = this.abq_user.id;
    this.warehouse_list()
    this.get_brand();
    this.get_product_category();
    this.get_all_product();
    console.log("done");
  }


  product:any={};
  active:any = {};
  invoice_date:any='';
  toggleterritory(key,action)
  {
    console.log(action);
    console.log(key);
    
    if(action == 'open')
    { this.active[key] = true; }
    if(action == 'close')
    { this.active[key] = false;}

    console.log(this.active);
  }

  warehouse:any=[];
  warehouse_list()
  {
    this.db.getData({'warehouse_id':this.productdata,'search':this.product},"warehouse/stock_warehouse_list")
    .subscribe(resp=>{
      console.log(resp);
      this.warehouse = resp['warehouse_list'];
    })
  }
  
  brand_list:any=[];
  get_brand()
  {
    this.db.getData({'warehouse_id':this.productdata,'search':this.product},"warehouse/stock_transfer_brand")
    .subscribe(resp=>{
      console.log(resp);
      this.brand_list=resp["brand"];
    });
  }
  
  
  category_list:any=[];
  get_product_category()
  {
    this.db.getData({'warehouse_id':this.productdata,'search':this.product},"warehouse/stock_transfer_category")
    .subscribe(resp=>{
      console.log(resp);
      this.category_list=resp['category'];
    });
  }
  
  product_list:any=[];
  get_all_product()
  {
    this.loader="1";
    this.db.getData({'warehouse_id':this.productdata,'search':this.product},"warehouse/stock_transfer_product")
    .subscribe(resp=>{
      this.product_list=resp["product_list"];
      this.fltr_pcode=this.product_list;
      console.log(this.product_list);
      console.log(this.product_list);

      if(this.new_stock.length > 0)
      {
        for(var j=0;j<this.product_list.length;j++)
        {
          for(var k=0;k<this.new_stock.length;k++)
          {
            if(this.product_list[j]['id'] == this.new_stock[k]['product_id'])
            {
              this.product_list[j]['current_stock'] =  this.product_list[j]['current_stock'] - this.new_stock[k]['qty'];
            }
          }
        }
      }

      this.loader="";
    });
  }
  
  fltr_pcode:any=[];
  pcode_array_filter(data,array,index_val)
  {
    this.fltr_pcode=this.filter(data.toUpperCase(),array,index_val);
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

  
  set_values(data)
  {
    console.log(data);
    // this.product={};
    this.product.product_id = data.id;
    this.product.name = data.product_name;
    this.product.category = data.category;
    this.product.sub_category = data.sub_category;
    this.product.brand = data.brand;
    this.product.price = data.price;
    this.product.current_stock = data.current_stock;
  }
  

  stock_list:any=[];
  new_stock:any=[];
  savingData = false;
  add_stock()
  {
    console.log("add stock");    
    console.log(this.product);    
    console.log(this.new_stock);    
    if(this.new_stock.length == 0 )
    {     
      this.new_stock.push(this.product);   
    }
    else
    {
      for(var i=0; i<this.new_stock.length; i++) 
      {
        if(this.new_stock[i].product_id == this.product.product_id) 
        {
          this.new_stock[i].qty = parseInt(this.new_stock[i].qty) + parseInt(this.product.qty);
          break;
        }
        else if(i == this.new_stock.length -1) 
        {
          this.new_stock.push(this.product);
          break; 
        }
      } 
    }
    console.log(this.new_stock);    
    this.product={};
    // this.get_brand();
    // this.get_product_category()
    this.get_all_product();
  }

  total_qty:any=0;
  submit_record()
  {
    for(let i=0;i<this.new_stock.length;i++)
    {
      this.total_qty=parseInt(this.total_qty) + parseInt(this.new_stock[i]['current_stock']);
    }
    this.data.product_data = this.new_stock;
    this.data.warehouse_from = this.productdata;
    this.data.total_qty = this.total_qty;
    console.log(this.new_stock);

    this.db.getData(this.data,"stock/submit_stock_transfer")
    .subscribe(resp=>{
      console.log(resp);
      if(resp == 'success')
      {
        this.route.navigate(['/stock-list']);
      }
    });
    
  }

  check_qty()
  {
    console.log(this.product.qty);
    console.log(this.product.current_stock);
    if(this.product.qty > this.product.current_stock)
    {
      this.toastr.errorToastr("Quantity Limit is : "+this.product.current_stock);
      this.product.qty = this.product.current_stock;
    }
  }
  
}
