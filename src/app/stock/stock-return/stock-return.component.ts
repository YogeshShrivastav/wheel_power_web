import { Component, OnInit } from '@angular/core';
import { slideToTop } from '../../router-animation/router-animation.component';
import { DatabaseService } from 'src/app/database.service';
import { Router } from '@angular/router';
import { SessionService } from 'src/app/session.service';
import { ToastrManager } from 'ng6-toastr-notifications';

@Component({
  selector: 'app-stock-return',
  templateUrl: './stock-return.component.html',
  animations: [slideToTop()]

})
export class StockReturnComponent implements OnInit {

  data:any={};
  productdata:any=0;
  formData=new FormData;
  loader:any='';
  constructor(public db:DatabaseService,public route:Router,public session:SessionService,public toastr:ToastrManager) { }
  select_product:any=[];
  session_data:any=[];
  abq_user:any=[];
  ngOnInit() {
    this.session_data=this.session.GetSession();
    this.abq_user=this.session_data.value;
    this.productdata = this.abq_user.id;
    console.log(this.abq_user.id);
    this.get_all_product();
    this.get_brand();
    this.get_vendor();
    this.get_category();
  }
  product:any={};
  active:any = {};
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

  vendor_list:any=[];
  get_vendor()
  {
    this.db.getData("","manufacturers/get_manufacturer_list")
    .subscribe(resp=>{
      console.log(resp);
      this.vendor_list=resp["data"];
      console.log(this.vendor_list);
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

  get_category()
  {
    this.db.getData({'warehouse_id':this.productdata,'search':this.product},"warehouse/stock_transfer_category")
    .subscribe(resp=>{
      console.log(resp);
      this.category_list=resp['category'];
    });
  }

  category_list:any=[];
  get_product_category(category)
  {
    this.db.getData(category,"products/get_product_category")
    .subscribe(resp=>{
        console.log(resp);
        this.category_list=resp
    });
  }
  get_detail(data)
  {
    console.log(data);    
    this.product.name=data.product_name;
    this.select_product=data;
  }

  selectedFile:any=[];
  selectFile(event)
  {
    console.log(this.data);
    this.selectedFile.push(event.target.files[0]);
    console.log(this.selectedFile);
  }


  submit_stock()
  {
    this.savingData = true;
    // this.data.type="add";
    this.data.warehouse_id=this.abq_user.id;
    this.data.product_arr=this.new_stock;
    this.formData.append('image',this.selectedFile[0],this.selectedFile.name);
    console.log(this.data);

    for(var property in this.data)
    {
      this.formData.append(property,JSON.stringify(this.data[property]));
    }
    
    this.db.uploadImage(this.formData,"stock/submit_stock_return")
    .subscribe(resp=>{
      console.log(resp);
      this.savingData = false;
      if(resp)
      {
        this.route.navigate(["/stock-list"]);
      }
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
    this.get_all_product();
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
