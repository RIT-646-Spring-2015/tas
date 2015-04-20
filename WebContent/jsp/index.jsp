<%@ page language="java" contentType="text/html; charset=ISO-8859-1"
	pageEncoding="ISO-8859-1"%>

<%@ taglib prefix="c" uri="http://java.sun.com/jsp/jstl/core"%>
<c:url value="topics" var="imagePath" />

<!DOCTYPE HTML>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1">
<title>TAS | Topic Approval System</title>

<link rel="icon" type="image/png" href="/${imagePath}/images/....png" />
<link rel="stylesheet" type="text/css"
	href="/${imagePath}/css/mainStyle.css" />

</head>
<body>
	<%
	    // This scriptlet declares and initializes "date"
	    // System.out.println( "Evaluating date now" );
	    java.util.Date date = new java.util.Date();
	%>
	<p>
		Hello! And Welcome to <span class="big">Team Win's Topic
			Approval System!</span>
	</p>
	<p class='left-indent'>More great stuff coming soon!</p>
	<p>
		The time is now
		<%
	    out.println( date );
	%>
	</p>
	<p>
		<%
		    out.println( "Your machine's address is " );
		    out.println( request.getRemoteHost() );
		%>
	</p>

</body>
</html>