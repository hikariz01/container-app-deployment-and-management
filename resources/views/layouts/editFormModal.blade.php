<div class="modal fade" id="editForm" tabindex="-1" aria-labelledby="editFormLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editFormLabel">Edit Resource</h5>
                <form action="{{ route('download-file') }}" method="POST" onsubmit="updateDownloadData()" style="margin-left: 20px">
                    @csrf
                    <input type="hidden" style="display: none" name="resourceName" id="resourceName">
                    <input type="hidden" style="display: none" name="downloadData" id="downloadData">
                    <button type="submit" class="btn btn-success">Download Code</button>
                </form>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('edit') }}" method="POST" onsubmit="updateData()">
                @csrf
                <div class="modal-body" id="editorContainer">
                    <div id="editor">//test</div>
                </div>

                <input type="hidden" name="value" style="display: none" id="editorValue" value="">
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save changes</button>
                </div>
            </form>
        </div>
    </div>
</div>
