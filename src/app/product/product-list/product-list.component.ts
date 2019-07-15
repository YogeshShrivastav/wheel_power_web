
import { Component, OnInit, Renderer2 } from '@angular/core';
import { slideToTop } from '../../router-animation/router-animation.component';

import { DatabaseService } from 'src/app/database.service';
import { DialogService } from 'src/app/dialog.service';
import { ToastrManager } from 'ng6-toastr-notifications';
import { SessionService } from 'src/app/session.service';
//import { DialogService } from 'src/app/dialog.service';

@Component({
  selector: 'app-product-list',
  templateUrl: './product-list.component.html',
  animations: [slideToTop()]
})
export class ProductListComponent implements OnInit {

  constructor(public db:DatabaseService,private renderer: Renderer2,public dialog:DialogService,public toastr:ToastrManager,public session:SessionService) { }
  session_data:any={};
  abq_user:any={};
  ngOnInit() {
    this.session_data=this.session.GetSession();
    this.abq_user=this.session_data.value;
    console.log(this.abq_user.login_type);
    this.get_product(100,0);
    this.get_product_brand();
  }
  search:any={};
  product_list:any=[];
  data:any=[];
  pagelimit:any=100;
  start:any=0;
  totalpage:any=0;
  pagenumber:any=0;
  product_cn:any=0;
  product:any={};
  loader:any="1";
  get_product(pagelimit:any=100,start:any=0)
  {
    this.pagelimit=parseInt(pagelimit);
    this.start=parseInt(start);
    this.db.getData({"pagelimit":this.pagelimit,"start":this.start,"search":this.product},"products/get")
    .subscribe(resp=>{
        console.log(resp);
        this.product_list=resp["data"];
        this.product_cn=resp["product_cn"];
        this.totalpage=Math.ceil(this.product_cn/this.pagelimit);
        this.pagenumber=Math.ceil(this.start/this.pagelimit)+1;
        console.log("data");        
        console.log(this.product_list);
        if(this.product_cn == 0){
          this.toastr.errorToastr("As per Entered Keywords..","No Any Product found!");
        }        
        this.loader="";
    });
  }

  refresh()
  {
    this.product={};
    this.get_product();
  }

  clear()
  {
    this.product={};
  }

  show:boolean=false;
  show_filter(data)
  {
    this.product={};
    if( data=='show')
    {
     this.show=true;
    }
    if(data == 'hide')
    {
      this.show=false;
      this.get_product();
    }
  }

  deleteproduct(id,indx)
  {    
    this.dialog.delete("Product")
    .then(resp=>{
      if(resp)
      {
        this.db.getData(id,"products/delete")
        .subscribe(resp=>{
        console.log(resp);
        this.product_list.splice(indx,1);
        this.toastr.successToastr("Deleted","Success!");
        });
      }
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
    });
  }

  subcategory_list:any=[];
  get_product_subcategory(brand,category)
  {
    console.log(brand);
    console.log(category);
    this.db.getData({"brand":brand,"category":category},"products/get_product_sub_category")
    .subscribe(resp=>{
        console.log(resp);
        this.subcategory_list=resp
        this.fltr_sub_category=this.subcategory_list;
    });
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
