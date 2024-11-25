import { Component, EventEmitter, Input, Output } from "@angular/core";
import { FileUploadModule } from "primeng/fileupload";
import { CommonModule } from "@angular/common";

@Component({
  selector: "app-file-upload",
  templateUrl: "./file-upload.component.html",
  styleUrls: ["./file-upload.component.scss"],
  standalone: true,
  imports: [FileUploadModule, CommonModule],
  providers: [],
})
export class FileUploadComponent {
  @Input() previewUrl: String = "";
  @Input() uploadedFiles: File[] = [];
  @Output() fileSelected = new EventEmitter<File[]>();

  choose(event: any, callback: any) {
    callback();
  }

  getFileName(index: number) {
    console.log(this.uploadedFiles.length, this.previewUrl);
    if (this.uploadedFiles.length > 0) {
      return this.uploadedFiles[index].name;
    } else if (
      this.previewUrl !== null &&
      this.previewUrl !== "" &&
      !this.previewUrl.hasOwnProperty("changingThisBreaksApplicationSecurity")
    ) {
      const urlSegments = this.previewUrl.split("/");
      return urlSegments[urlSegments.length - 1];
    }
  }

  onFileSelected(event: any) {
    if (event) {
      console.log("ici", event);
      const file = event.files[0];
      this.uploadedFiles = [event.files[0]];
      this.previewUrl = file.objectURL;
      this.fileSelected.emit(file);
    }
  }

  onRemoveFile(event: any) {
    this.previewUrl = "";
  }
}
