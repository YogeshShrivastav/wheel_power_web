import { Component, OnInit } from '@angular/core';
import { slideToTop } from '../../router-animation/router-animation.component';

@Component({
  selector: 'app-annoucement-list',
  templateUrl: './annoucement-list.component.html',
  animations: [slideToTop()]

})
export class AnnoucementListComponent implements OnInit {

  constructor() { }

  ngOnInit() {
  }

}
