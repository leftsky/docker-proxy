import React, { PureComponent } from 'react'
import _ from "loadsh";
import styles from './sites.module.scss'

export default class sites extends PureComponent {
  state = {
    sites: [{
      name: "个人博客",
      ip: "192.168.191.2",
      ssl_status: "正常 2021-12-01 到期"
    }]
  }

  componentDidMount() {
    console.log("Hello world")
  }

  render() {
    const { sites } = this.state;
    return (
      <div className={styles.boxes}>
        {_.map(sites, item => {
          return <div className={styles.boxItem}>
            <div>名称：{item.name}</div>
            <div>IP: {item.ip}</div>
            <div>SLL证书状态：{item.ssl_status}</div>
          </div>
        })}
      </div>
    )
  }
}
