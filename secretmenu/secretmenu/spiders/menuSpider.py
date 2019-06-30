import scrapy
import time

class MenuSpider(scrapy.Spider):
    name = "menu"
    page_number = 2

    start_urls = ["https://starbuckssecretmenu.net/"]

    def parse(self, response): 
        categ = response.css(".menu-categ-container > ul > li > a::attr(href)").getall()
        frappuccinos_url = categ[1]
        drinks_url = categ[2]
        teas_url = categ[3]
        yield scrapy.Request(frappuccinos_url, callback=self.parse_page)
        yield scrapy.Request(drinks_url, callback=self.parse_page)
        yield scrapy.Request(teas_url, callback=self.parse_page)


    def parse_page(self, response):
        for row in response.css(".td-block-span6"):
            detail_page = row.css("a::attr(href)").extract_first()
            url = response.urljoin(detail_page)
            yield scrapy.Request(url, callback = self.parse_detail)
            break

        icon = response.css(".td-icon-menu-right").extract_first()
        if icon is not None:
            pages = response.css(".td-ss-main-content > div > a::attr(href)").getall()
            next_page = pages[-1]
            yield response.follow(next_page, callback=self.parse_page)

    def parse_detail(self, response):
        ret = {}

        # recipe of secret drink
        recipe = ""
        time.sleep(5)
        for row in response.css(".td-post-content ul li::text"):
            recipe += row.get().replace('\n',' ') + '. '
        recipe = recipe

        # if recipe exists then exit the method
        if not recipe:
            return

        # otherwise add info into the return dictionary
        # name of the secret drink
        name = response.css(".entry-title::text").extract_first()
        ret["name"] = self.parse_name(name)

        ret["recipe"] = recipe

        # category of secret menu
        category = response.css(".entry-crumb::text").getall()
        ret["category"] = category[1]

        # images of the secret drink
        images = []
        for image in response.css(".td-post-content img::attr(src)"):
            images.append(image.get())
        ret["images"] = images

        yield ret

    def parse_name(self, name):
        if "|" in name:
            return name[:name.index("|")-1]
        elif ":" in name:
            return name[name.index(":")+2:]
        else:
            return name
