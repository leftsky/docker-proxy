import React, { PureComponent } from 'react'
import _ from "loadsh";
import styles from './sites.module.scss'
import wsapi from './websocket-api'

export default class sites extends PureComponent {
  state = {
    sites: []
  }

  componentDidMount() {
    this.domain = "";
    setTimeout(() => {
      this.getSites();
    }, 500);
  }

  getSites() {
    wsapi.sendCmd("getSites", {}).then(res => {
      console.log(res)
      this.setState({
        sites: res
      })
    })
  }

  newSites = () => {
    if (this.domain === "") {
      return;
    }
    console.log(this.domain);
    wsapi.sendCmd("newSite", {
      domains: this.domain,
      dest_ip: this.dest_ip,
      name: this.name
    }).then(res => {
      this.getSites();
    })
  }

  delSite = (domain) => {
    wsapi.sendCmd("delSite", {
      domain: domain
    }).then(res => {
      this.getSites();
    })
  }

  newSSL = (domain) => {
    wsapi.sendCmd("newSSL", { domain }).then(res => {
      console.log(res)
    })
  }

  render() {
    const { sites } = this.state;
    return <div>
      <div className={styles.boxes}>
        {_.map(sites, item => {
          let domain = (item && item.domains) ? item.domains[0] : '未知';
          return <div key={domain} className={styles.boxItem}>
            <div>域名：{domain}</div>
            <div>名称：{item.name}</div>
            <div>IP: {item.vhost_dest_ip}</div>
            <div onClick={() => this.newSSL(domain)}>SLL证书状态：{item.use_ssl ? '可用' : '不可用'}</div>
            <button onClick={() => this.delSite(domain)}>X</button>
          </div>
        })}
      </div>
      <div className={styles.addBox}>
        <input onKeyUp={e => { this.domain = e.target.value }} placeholder="域名" />
        <input onKeyUp={e => { this.dest_ip = e.target.value }} placeholder="容器IP" />
        <input onKeyUp={e => { this.name = e.target.value }} placeholder="网站名称" />
        <button onClick={this.newSites}>新增网站</button>
      </div>
    </div>
  }
}
