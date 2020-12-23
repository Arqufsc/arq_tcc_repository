const render = {
    loading: (container)=>{
        const divAwait = document.createElement('div')
        const text = document.createTextNode('Lendo páginas...')
        divAwait.setAttribute('id', 'loading')
        divAwait.appendChild(text)

        container.appendChild(divAwait)
    },

    endToRead: (container)=>{
        const endParagraph = document.createElement('p')
        const endParagraphText = document.createTextNode('Fim da leitura!')
        endParagraph.appendChild(endParagraphText)
        container.appendChild(endParagraph)
    },

    readingPage: (container, page)=>{
        const paragraph = document.createElement('p')
        const paragraphText = document.createTextNode(`Lendo ${(page +1)}ª página do repositório`)
        paragraph.appendChild(paragraphText)
        container.appendChild(paragraph)
    },

    unordenedList: (container, className)=>{
        const ul = document.createElement('ul')
        ul.setAttribute('class', className)

        container.appendChild(ul)

        return ul
    },

    itemList: (container)=>{
        const li = document.createElement('li')
        container.appendChild(li)

        return li
    },

    trbInfo: (container, trb)=>{
        const link = document.createElement('a')
        link.setAttribute('href', trb.url)
        link.setAttribute('target', '_blank')

        const linkText = document.createTextNode(trb.title)
        link.appendChild(linkText)

        const author = document.createElement('p')
        const authorLabel = document.createTextNode('Autor: ')
        const authorName = document.createElement('strong')
        const authorNameText = document.createTextNode(trb.author)
        authorName.appendChild(authorNameText)
        author.appendChild(authorLabel)
        author.appendChild(authorName)

        const year = document.createElement('p')
        const yearLabel = document.createTextNode('Autor: ')
        const yearName = document.createElement('strong')
        const yearNameText = document.createTextNode(trb.year)
        yearName.appendChild(yearNameText)
        year.appendChild(yearLabel)
        year.appendChild(yearName)

        container.appendChild(link)
        container.appendChild(author)
        container.appendChild(year)

    },

    details: (container, summaryText, className)=>{
        const details = document.createElement('details')
        details.setAttribute('class', className)

        const summary = document.createElement('summary')
        const summaryNodeText = document.createTextNode(summaryText)
        summary.appendChild(summaryNodeText)
        details.appendChild(summary)

        container.appendChild(details)

        return details
    },

    title: (container, tag, text)=>{
        const title = document.createElement(tag)
        const titleText = document.createTextNode(text)
        title.appendChild(titleText)
        container.appendChild(title)
    },

    tag: (container, tag, props={})=>{
        let htmlElement = document.createElement(tag)
        
        htmlElement = setProps(htmlElement, props)

        container.appendChild(htmlElement)

        return htmlElement
    }
}

function setProps(htmlElement, props){
    for(let propName in props){
        if(propName == 'text'){
            const htmlInnerText = document.createTextNode(props.text)
            htmlElement.appendChild(htmlInnerText)
        }else{
            htmlElement.setAttribute(propName, props[propName])
        }
    }

    return htmlElement
}

export default render;