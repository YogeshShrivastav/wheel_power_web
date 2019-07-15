import { Component, OnInit } from '@angular/core';
import { SessionService } from 'src/app/session.service';
import { DatabaseService } from 'src/app/database.service';
import { ActivatedRoute, Router } from '@angular/router';
import { DialogService } from 'src/app/dialog.service';

@Component({
  selector: 'app-stock-update',
  templateUrl: './stock-update.component.html',
})
export class StockUpdateComponent implements OnInit {

  session_data:any=[];
  abq_user:any=[];
  slip_id:any=0;
  productdata:any=0;
  loader:any="";
  data:any={};
  product:any={};
  constructor(public session:SessionService,public db:DatabaseService,public route:Router,public dialog:DialogService) { }

  ngOnInit() {
    this.session_data=this.session.GetSession();
    this.abq_user=this.session_data.value;
    this.productdata = this.abq_user.id;
    console.log(this.productdata);
    this.get_brand();
    this.get_product_category();
    this.get_all_product();
  }


  brand_list:any=[];
  get_brand()
  {
    this.db.getData("","products/get_product_brand")
    .subscribe(resp=>{
      console.log(resp);
      this.brand_list=resp;
    });
  }
  
  
  category_list:any=[];
  get_product_category()
  {
    this.db.getData("","products/get_product_category_indp")
    .subscribe(resp=>{
      console.log(resp);
      this.category_list=resp;
    });
  }
  
  product_list:any=[];
  get_all_product()
  {
    this.loader="1";
    this.db.getData({'warehouse_id':this.productdata},"products/get_products_for_update")
    .subscribe(resp=>{
      console.log(resp);
      
      this.product_list=resp["data"];
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
              this.product_list[j]['current_stock'] = parseInt(this.new_stock[k]['current_stock']);
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
          this.new_stock[i].current_stock = parseInt(this.new_stock[i].current_stock) + parseInt(this.product.current_stock);
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
    this.get_all_product();
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

  update_stock()
  {
    this.data.stock = this.new_stock;
    this.data.warehouse_id = this.productdata;

    this.db.getData(this.data,"stock/update_stock")
    .subscribe(resp=>{
      console.log(resp);
      if(resp == 'success')
      {
        this.dialog.success("Success","Stock Updated");
        this.route.navigate(['/stock-list']);
      }
    })
  }
}
