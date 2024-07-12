<!DOCTYPE html>
<html lang="tr">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Language" content="tr">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Öğrenci Tablosu</title>
</head>
 

<body>

    <?php


    $conn = connect();
    $page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
    $sort = isset($_GET['sort']) ? $_GET['sort'] : 'sid';
    $order = isset($_GET['order']) ? $_GET['order'] : 'DESC';
    $search = isset($_GET['search']) ? $_GET['search'] : null;
    echo pageHeader("Student List");
    listele($page, $sort, $order);
    echo pageFooter();

    function connect()
    {
        $servername = "localhost";
        $username = "mustafa";
        $password = "1234";
        $dbname = "sdb";

        $conn = new mysqli($servername, $username, $password, $dbname) or die("Connection failed: " . $conn->connect_error);
        return $conn;
    }

    function pageHeader($heading)
    {
        return "<html><head><title>$heading</title></head><body>";
    }

    function pageFooter()
    {
        return "</body></html>";
    }

    function buildUrl($extraQueries = [])
    {
        global $search, $sort, $order, $page;
        $params = [];
        if ($search) {
            $params['search'] = $search;
        }
        if ($sort && $sort !== 'sid') {
            $params['sort'] = $sort;
        }
        if ($order && $order !== "ASC") {
            $params['order'] = $order;
        }
        if ($page && $page > 1) {
            $params['page'] = $page;
        }
        foreach ($extraQueries as $key => $value) {
            $params[$key] = $value;
        }

        return http_build_query($params);
    }

    function listele($page, $sort, $order)
    {
        $buildUrl = 'buildUrl';
        global $conn;


        $urlQuery = "?";
        $query = 'SELECT * from studentdb ';

        if ($sort) {
            $query = $query . 'ORDER BY ' . $sort . ' ' . $order . ' ';
            $urlQuery = $urlQuery . "&sort=$sort";
        }
        $result = mysqli_query($conn, $query) or die(mysqli_error($conn));
        $limit = 5;
        $totalRecords = mysqli_num_rows($result);
        $totalPages = ceil($totalRecords / $limit);

        if ($page) {
            $limit = 5;
            $offset = ($page - 1) * $limit;
            $query = $query . 'LIMIT ' . $limit . ' OFFSET ' . $offset . ' ';
            $urlQuery = $urlQuery . "&page=$page";
        }
        $query = $query . ";";

        $result = mysqli_query($conn, $query) or die(mysqli_error($conn));

        # $page = max($page, $totalPages);
        $prevPage = max($page - 1, 1);
        $nextPage = min($page + 1, $totalPages);

        #echo "totalRecord: $totalRecords , totalPages: $totalPages , page: $page <br>";
    

        $isim = isset($_GET['isim']) ? htmlspecialchars($_GET['isim']) : '';
        $soyisim = isset($_GET['soyisim']) ? htmlspecialchars($_GET['soyisim']) : '';
        $dogumYeri = isset($_GET['dogumYeri']) ? htmlspecialchars($_GET['dogumYeri']) : '';
        $dogumTarihi = isset($_GET['dogumTarihi']) ? htmlspecialchars($_GET['dogumTarihi']) : '';



        echo "<style>td {border:1px solid red}</style>
            
                <table id='studentsTable'>
                    <tr>
                        <td><a href='?{$buildUrl(['sort' => 'sid', 'order' => $order === 'ASC' ? 'DESC' : 'ASC'])}'>NO</a></td>
                        <td><a href='?{$buildUrl(['sort' => 'fname', 'order' => $order === 'ASC' ? 'DESC' : 'ASC'])}'>AD</a></td>
                        <td><a href='?{$buildUrl(['sort' => 'lname', 'order' => $order === 'ASC' ? 'DESC' : 'ASC'])}'>SOYAD</a></td>
                        <td><a href='?{$buildUrl(['sort' => 'birthPlace', 'order' => $order === 'ASC' ? 'DESC' : 'ASC'])}'>DOĞUM YERİ</a></td>
                        <td><a href='?{$buildUrl(['sort' => 'birthDate', 'order' => $order === 'ASC' ? 'DESC' : 'ASC'])}'>DOĞUM TARİHİ</a></td>
                    </tr>";

        echo "
        <tr>
        <td>
            <td><input type='text' id='isim' name='isim' required></td>
            <td><input type='text' id='soyisim' name='soyisim' required></td>
            <td><input type='text' id='dogumYeri' name='dogumYeri' required></td>
            <td><input type='date' id='dogumTarihi' name='dogumTarihi' required></td>
            <td><button class='action-button' onclick='addRow(this)'>Add</button></td>
            <td><button type='reset' class='add-button'>reset</button>
            </td> 
            </tr>";

        echo "<tr>
                    <td></td>
                            <td><input type='text' id= 'searchFname' value='$isim'></td>
                            <td><input type='text' id= 'searchLname' value='$soyisim'></td>
                            <td><input type='text' id= 'searchBirthplace' value='$dogumYeri'></td>
                            <td><input type='date' id ='searchBirthdate' value='$dogumTarihi'></td>
                            <td><button onclick='searchRow()'>Ara</button></td>
                            <td><button type='button' class='add-button' onclick='window.location.href=window.location.pathname'>Clear</button></td>
                        </tr>";




        $result = $conn->query($query);
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo "<tr id='{$row['sid']}'>
                    
                        <td>{$row['sid']}</td>
                        <td data-col='fname'>{$row['fname']}</td>
                        <td data-col='lname'>{$row['lname']}</td>
                        <td data-col='birthPlace'>{$row['birthPlace']}</td>
                        <td data-col='birthDate'>{$row['birthDate']}</td>
                        <td><button data-action='delete' class='action-button' onclick='deleteRow({$row['sid']})'>Sil</button></td>
                        <td> <button data-action='update'>Güncelle</button>
                             <button data-action='save' hidden>Sakla</button>
                         </td>                        
                    </tr>\n";
            }
        } else {
            echo "0 results";

        }

        echo "</table>";

        echo '<div style="margin-left:200px; margin-top:20px; font-size:larger">';
        if ($page != 1) {
            $x = $_SERVER['QUERY_STRING'] . "&" . $buildUrl(['page' => 1]);
            echo "<div><a href='?{$x}'>&lt;&lt;</a> ";

            $x = $_SERVER['QUERY_STRING'] . "&" . $buildUrl(['page' => $prevPage]);
            echo "<a href='?{$x}'>&lt;</a>  ";
        }
        for ($i = max(1, $page - 3); $i <= min($totalPages, $page + 3); $i++) {
            $x = $_SERVER['QUERY_STRING'] . "&" . $buildUrl(['page' => $i]);
            echo "<a href='?{$x}'>$i</a> ";
        }
        if ($page != $totalPages) {
            $x = $_SERVER['QUERY_STRING'] . "&" . $buildUrl(['page' => $nextPage]);
            echo "<a href='?{$x}'>&gt;</a>  ";

            $x = $_SERVER['QUERY_STRING'] . "&" . $buildUrl(['page' => $totalPages]);
            echo "<a href='?{$x}'>&gt;&gt;</a></div>";
        }
        echo '</div>';
        mysqli_close($conn) or die(mysqli_error($conn));
    }

    ?>

    <script>

        window.onload = function () {
            document.addEventListener('click', function (e) {
                let currentRow = e.target.closest('tr')
                if (!currentRow) return;
                let sid = currentRow.id
                if (e.target.hasAttribute('data-action')) {
                    toggleButtons(sid)
                    if (e.target.getAttribute('data-action') === 'update') {
                        // Turn all tds to contenteditable at the same row
                        currentRow.querySelectorAll('[data-col]').forEach(col => {
                            col.contentEditable = true
                            col.style.backgroundColor ="silver";

                            
                        })
                    }
                    else if (e.target.getAttribute('data-action') === 'save') {

                        let newFname = currentRow.querySelector("[data-col='fname']").innerText
                        let newlname = currentRow.querySelector("[data-col='lname']").innerText
                        let newDogumYeri = currentRow.querySelector("[data-col='birthPlace']").innerText
                        let newDogumTarihi = currentRow.querySelector("[data-col='birthDate']").innerText


                        currentRow.querySelectorAll('[data-col]').forEach(col => {
                            col.contentEditable = false
                            col.style.backgroundColor ="white";
                        })
                        saveRow(sid, newFname, newlname, newDogumYeri, newDogumTarihi);

                    }
                }
            })
        };

        function toggleButtons(id) {
            let row = document.getElementById(id)
            let update = row.querySelector("[data-action='update']")
            let save = row.querySelector("[data-action='save']")
            update.hidden = !update.hidden
            save.hidden = !save.hidden
        }

        function deleteRow(sid) {
            if (deleteFromDatabase(sid)) {
                deleteTRFromTABLE(sid);
                alert('kayıt başarıyla silind ( 1 )')
            }
            else
                alert('Unable to delete record from database ( 0 )');
        }
        function deleteFromDatabase(sid) {
            var sonuc = false;
            const xhttp = new XMLHttpRequest();
            xhttp.onload = function () {
                var sonuc = Number(this.responseText);
            }
            xhttp.open("GET", "delete_student.php?sid=" + sid, true);
            xhttp.send();
            return true;
        }
        function deleteTRFromTABLE(sid) {

            var tr = document.getElementById(sid);
            tr.remove();
        }


        function addRow() {
            var isim = document.getElementById("isim").value;
            var soyisim = document.getElementById("soyisim").value;
            var dogumYeri = document.getElementById("dogumYeri").value;
            var dogumTarihi = document.getElementById("dogumTarihi").value;
            let data = new FormData()
            data.append('isim', isim)
            data.append('soyisim', soyisim)
            data.append('dogumYeri', dogumYeri)
            data.append('dogumTarihi', dogumTarihi)
            fetch("add_student.php", {
                method: "POST",
                body: data
            }).then(res => {
                res.json().then(body => {
                    var table = document.getElementById("studentsTable").getElementsByTagName('tbody')[0];
                    var newRow = table.insertRow(3);
                    newRow.setAttribute("id", body.data.id)
                    var cell1 = newRow.insertCell(0);
                    var cell2 = newRow.insertCell(1);
                    cell2.setAttribute('data-col', 'fname')
                    var cell3 = newRow.insertCell(2);
                    cell3.setAttribute('data-col', 'lname')
                    var cell4 = newRow.insertCell(3);
                    cell4.setAttribute('data-col', 'birthPlace')
                    var cell5 = newRow.insertCell(4);
                    cell5.setAttribute('data-col', 'birthDate')
                    var cell6 = newRow.insertCell(5);
                    var cell7 = newRow.insertCell(6);

                    cell1.innerHTML = body.data.id;
                    cell2.innerHTML = body.data.isim;
                    cell3.innerHTML = body.data.soyisim;
                    cell4.innerHTML = body.data.dogumYeri;
                    cell5.innerHTML = body.data.dogumTarihi;
                    cell6.innerHTML = "<button class='action-button' onclick='deleteRow(" + body.data.id + ")'>Sil</button>";
                    cell7.innerHTML = "<button data-action='update'>Güncelle</button> <button data-action='save' hidden>Sakla</button>";



                    document.getElementById("isim").value = "";
                    document.getElementById("soyisim").value = "";
                    document.getElementById("dogumYeri").value = "";
                    document.getElementById("dogumTarihi").value = "";
                })

            }, (err) => {
                console.log("Error:", err);
            })
        }

 function saveRow(sid, newFname, newlname, newDogumYeri, newDogumTarihi) {

            var table = document.getElementById("studentsTable");

            const params = new URLSearchParams();
            params.append('sid', sid);
            params.append('newfname', newFname);
            params.append('newlname', newlname);
            params.append('newDogumYeri', newDogumYeri);
            params.append('newDogumTarihi', newDogumTarihi);


            var xhr = new XMLHttpRequest();
            xhr.open("POST", "update_student.php", true);
            xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
            xhr.onreadystatechange = function () {
                if (xhr.readyState === XMLHttpRequest.DONE && xhr.status === 200) {
                    var response = xhr.responseText;
                    alert(response);
                }
            };
            xhr.send("sid=" + sid + "&isim=" + newFname + "&soyisim=" + newlname + "&dogumYeri=" + newDogumYeri + "&dogumTarihi=" + newDogumTarihi);

        }


        function searchRow() {
            var search_name = document.getElementById("searchFname").value;
            var search_lastName = document.getElementById("searchLname").value;
            var search_birthPlace = document.getElementById("searchBirthplace").value;
            var search_birthDate = document.getElementById("searchBirthdate").value;

            var xhr = new XMLHttpRequest();
            xhr.open("POST", "search_student.php", true);
            xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
            xhr.onreadystatechange = function () {
                if (xhr.readyState === XMLHttpRequest.DONE && xhr.status === 200) {
                    var response = JSON.parse(xhr.responseText);
                    console.log(response);
                    if (response.error) {
                        alert("Query error: " + response.error);
                    } else {
                        displayResults(response);
                    }
                }
            };
            xhr.send("isim=" + search_name + "&soyisim=" + search_lastName + "&dogumYeri=" + search_birthPlace + "&dogumTarihi=" + search_birthDate);
        }

        function displayResults(results) {
            var table = document.getElementById("studentsTable");
            table.querySelectorAll("tr[id]").forEach(row => row.remove())

            results.forEach(function (result) {
                var row = table.insertRow();
                row.id = result.sid
                var cell1 = row.insertCell(0);
                var cell2 = row.insertCell(1);
                var cell3 = row.insertCell(2);
                var cell4 = row.insertCell(3);
                var cell5 = row.insertCell(4);
                var cell6 = row.insertCell(5);
                var cell7 = row.insertCell(6);

                cell1.innerText = result.sid;
                cell2.innerText = result.fname;
                cell3.innerText = result.lname;
                cell4.innerText = result.birthPlace;
                cell5.innerText = result.birthDate;
                cell6.innerHTML = "<button class='action-button' onclick='deleteRow(" + result.sid + ")'>Sil</button>";
                cell7.innerHTML = "<button data-action='update'>Güncelle</button> <button data-action='save' hidden>Sakla</button>";

            });
        }



    </script>

</body>

</html>