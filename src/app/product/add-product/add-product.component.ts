import { Component, OnInit, Renderer2 } from '@angular/core';
import { slideToTop } from '../../router-animation/router-animation.component';
import { DatabaseService } from 'src/app/database.service';
import { DialogService } from 'src/app/dialog.service';
import { SessionService } from 'src/app/session.service';
import { Router } from '@angular/router';

@Component({
  selector: 'app-add-product',
  templateUrl: './add-product.component.html',
  animations: [slideToTop()]
})
export class AddProductComponent implements OnInit {

  constructor(private renderer: Renderer2,public db:DatabaseService,public dialog:DialogService,public session:SessionService,public router:Router) { }

  session_data:any=[];
  abq_user:any=[];
  ngOnInit() {
    this.session_data=this.session.GetSession();
    this.abq_user=this.session_data.value;
    console.log(this.abq_user);
    this.get_product_brand();
  }

  product:any={category:'',subcategory:'',brand:'',product_name:'',product_code:'',unit:'',min_qty:'',price:''};
  feature:any=[];
  status:boolean = false;
  toggleHeader() {
      this.status = !this.status;
      if(this.status) {
          this.renderer.addClass(document.body, 'nav-active');
      }
      else {
          this.renderer.removeClass(document.body, 'nav-active');
      }
  }

  formData=new FormData();
  category_list:any=[];
  get_product_category(brand)
  {
    console.log(brand);
    
    this.db.getData(brand,"products/get_product_category")
    .subscribe(resp=>{
        console.log(resp);
        this.category_list=resp
        this.fltr_category=this.category_list;
    });
  }

  subcategory_list:any=[];
  get_product_subcategory(brand,category)
  {
    // console.log(category);

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

  savingData = false;
  new_str:any='';
  tmp_feature_list:any=[];
  add_product()
  {
    this.savingData = true;
    this.product.feature=this.tmp_feature_list;
    this.product.user_type=this.abq_user.id;
    console.log(this.product);
    
    for(var i=0; i<this.selectedFile.length; i++)
    {
      this.formData.append("image"+i,this.selectedFile[i],this.selectedFile[i].name);
    }
    for( var property in this.product)
    {
      this.formData.append(property,JSON.stringify(this.product[property]));
    }

    this.db.uploadImage(this.formData,"products/store")
    .subscribe(resp=>{
        console.log(resp);
        this.savingData = false;
        if(resp)
        {
          this.dialog.success("Success","Product Inserted");
          if(this.abq_user.login_type == 'admin')
          { this.router.navigate(["/product-list"]); }
          else
          { this.router.navigate(["/add-stock"]);  }
        }        
    });    
  }

  feature_list:any=[];
  selectedFile:any=[];
  storeAttrData(attr_type, attr_options)
  {
    if(attr_type)
    {
      this.feature_list.push({attr_type: attr_type, attr_options: attr_options.split(',')});
      this.feature.type = this.feature.value = null;
      console.log(this.feature_list);

      this.tmp_feature_list.push({type:attr_type,value:attr_options});
    }
  }
  
  delete_feature(index)
  {
    this.feature_list.splice(index,1);
  }

  already:any=false;
  check_product_code()
  {
    console.log(this.product.product_code);
    this.db.getData(this.product.product_code,"products/check_product")
    .subscribe(resp=>{
      console.log(resp);
      this.already = resp['exist'];
    })
  }

  imageSrc: any={};
  urls = new Array<string>();
  selectvalue(data)
  {
    // this.urls = [];
    let files = data.target.files;
    if (files) {
      for (let file of files) {
        let reader = new FileReader();
        reader.onload = (e: any) => {
          this.urls.push(e.target.result);
        }
        reader.readAsDataURL(file);
      }
    }
    console.log(data);
    console.log(data.target.files.length);
    for(var i=0; i<data.target.files.length; i++)
    {
      this.selectedFile.push(data.target.files[i]);
    }
  }

  removeImg(index)
  {
    this.urls.splice(index,1);
  }
  

  MobileNumber(event: any)
  {
    const pattern = /[0-9\+\-\ ]/;
    let inputChar = String.fromCharCode(event.charCode);
    if (event.keyCode != 8 && !pattern.test(inputChar)) {
      event.preventDefault();
    }
  }

  fltr_category:any=[];
  category_array_filter(data,array,index_val)
  {
    this.fltr_category=this.category_list;
    this.fltr_category=this.filter(data.toUpperCase(),array,index_val);
  }

  fltr_sub_category:any=[];
  subcategory_array_filter(data,array,index_val)
  {
    this.fltr_sub_category=this.category_list;
    this.fltr_sub_category=this.filter(data.toUpperCase(),array,index_val);
  }

  fltr_brand:any=[];
  brand_array_filter(data,array,index_val)
  {
    this.fltr_brand=this.category_list;
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

}
