const getOnServer = {

    getData: async (query, method='GET')=>{
        const response = new Promise((resolve, reject)=>{

            const request = new XMLHttpRequest()
    
            request.open(method, `./src/index.php${query}`, true)
            request.send()
            request.onreadystatechange = ()=>{
                if(request.readyState === 4){
                    if(request.status === 200){
                        //console.log(request.responseText)
                        resolve(JSON.parse(request.responseText))
                    }else{
                        reject(xmlhttp.status)
                    }
                }
            }
        })

        return response
    }
}

export default getOnServer