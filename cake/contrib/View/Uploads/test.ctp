<h2>Upload voice and transcode mp3/wav into m4a</h2>
<form action="/uploads/voice" method="post" enctype="multipart/form-data">
	<input type="file" accept="audio/*" name="data[voices][voice]" required="required">
	<input type="submit">
</form>

<h2>Upload cover and crop it</h2>
<form action="/uploads/cover" method="post" enctype="multipart/form-data">
	<input type="file" accept="image/*" name="data[voices][cover]" required="required">
	<table>
	    <tr>
	        <td>Left:</td><td><input type="text" name="data[voices][crop][left]" size="3" /></td>
	    </tr>
	    <tr>
	        <td>Top:</td><td><input type="text" name="data[voices][crop][top]" size="3" /></td>
	    </tr>
	    <tr>
	        <td>Width:</td><td><input type="text" name="data[voices][crop][width]" size="3" /></td>
	    </tr>
	    <tr>
	        <td>Height:</td><td><input type="text" name="data[voices][crop][height]" size="3" /></td>
	    </tr>
	</table>
	<input type="submit">
</form>

