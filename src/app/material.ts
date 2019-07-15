import { NgModule } from '@angular/core';

import 
{ 
    MatButtonModule,
    MatCheckboxModule,
    MatGridListModule,
    MatFormFieldModule,
    MatInputModule,
    MatMenuModule,
    MatRippleModule,
    MatAutocompleteModule,
    MatSelectModule,
    MatTableModule,
    MatPaginatorModule,
    MatDialogModule,
    MatDividerModule,
    MatRadioModule,
    MatDatepickerModule,
    MatNativeDateModule,
    MatProgressSpinnerModule,
    MatTabsModule,
    MatTooltipModule,
    MatSlideToggleModule
    
 } from '@angular/material';

@NgModule({
    imports: [
        MatButtonModule,
        MatCheckboxModule,
        MatGridListModule, 
        MatFormFieldModule,
        MatInputModule,
        MatMenuModule,
        MatRippleModule,
        MatAutocompleteModule,
        MatSelectModule,
        MatTableModule,
        MatPaginatorModule,
        MatDialogModule,
        MatDividerModule,
        MatRadioModule,
        MatDatepickerModule,
        MatNativeDateModule,
        MatProgressSpinnerModule,
        MatTabsModule,
        MatTooltipModule,
        MatSlideToggleModule
    ],
    
    exports: [
        MatButtonModule, 
        MatCheckboxModule,
        MatGridListModule,
        MatFormFieldModule,
        MatInputModule,
        MatMenuModule,
        MatRippleModule,
        MatAutocompleteModule,
        MatSelectModule,
        MatTableModule,
        MatPaginatorModule,
        MatDialogModule,
        MatDividerModule,
        MatRadioModule,
        MatDatepickerModule,
        MatNativeDateModule,
        MatProgressSpinnerModule,
        MatTabsModule,
        MatTooltipModule,
        MatSlideToggleModule
    ],
})
export class MaterialModule { }